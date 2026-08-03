#!/usr/bin/env bash
# End-to-end CRUD exercise against the running app over HTTP, behaving like a
# browser: real session cookie jar + CSRF token scraped from each form.
#
# SAFETY: this script mutates the live database, so every row it touches must be
# one it created itself. Each run stamps its tasks with a unique $RUN marker,
# resolves ids by matching that marker (never "the first row in the list"), and
# refuses to update, toggle or delete anything whose detail page doesn't carry
# the marker. An earlier version resolved the id with `head -1` and destroyed a
# real task; the guards below exist to make that impossible.
set -uo pipefail

BASE="${BASE:-http://172.26.6.118}"
JAR=$(mktemp)
RUN="ct$$x$RANDOM"                       # unique per run, no regex metacharacters
T1="Buy milk and eggs $RUN"
T2="Renew passport $RUN"
T1_EDITED="Buy oat milk $RUN"
PASS=0; FAIL=0
ID=""; ID2=""

ok()   { echo "  PASS: $1"; PASS=$((PASS+1)); }
bad()  { echo "  FAIL: $1"; FAIL=$((FAIL+1)); }
check() { if [ "$2" = "$3" ]; then ok "$1 ($2)"; else bad "$1 — expected '$3', got '$2'"; fi; }
contains() { if grep -qF -- "$2" "$3"; then ok "$1"; else bad "$1 — '$2' not found"; fi; }
absent()   { if grep -qF -- "$2" "$3"; then bad "$1 — '$2' unexpectedly present"; else ok "$1"; fi; }

# Scoped to the task rows only. The flash message ("Deleted “X”.") also contains
# the title, so a whole-page grep would give a false positive here.
titles_in() { grep -o 'class="task-title" href="[^"]*">[^<]*' "$1" | sed 's/.*>//'; }
absent_row() {
  if titles_in "$3" | grep -qF -- "$2"; then bad "$1 — '$2' still listed as a task"; else ok "$1"; fi
}

# GET a page into $2, echoing its CSRF token on stdout
token_from() {
  curl -sS -c "$JAR" -b "$JAR" -o "$2" "$1" --max-time 15
  grep -o 'name="_token" value="[^"]*"' "$2" | head -1 | sed 's/.*value="//;s/"//'
}
code() { curl -sS -c "$JAR" -b "$JAR" -o "$2" -w '%{http_code}' "$1" --max-time 15; }

# Id of the row whose title is exactly $2, read from that row's own show link.
# Never falls back to "some other row" — an unmatched title yields an empty id.
row_id() {
  sed -n "s|.*class=\"task-title\" href=\"[^\"]*/tasks/\([0-9]\{1,\}\)\">$2<.*|\1|p" "$1" | head -1
}

# The "All" tab's live count is the first .count span in the toolbar.
all_count() { grep -o 'class="count">[0-9]*' "$1" | head -1 | tr -cd '0-9'; }

# Refuse to mutate anything that isn't ours. Called before every write.
assert_ours() {
  local id="$1" what="$2"
  if [ -z "$id" ]; then bad "$what — refusing to act: no id resolved"; return 1; fi
  curl -sS -c "$JAR" -b "$JAR" -o /tmp/t_guard.html "$BASE/tasks/$id" --max-time 15
  if grep -qF -- "$RUN" /tmp/t_guard.html; then
    return 0
  fi
  bad "$what — SAFETY STOP: task $id is not marked '$RUN', it is not ours"
  return 1
}

# Remove every task this run created, whatever happened above.
cleanup() {
  local id t
  for id in $ID $ID2; do
    t=$(token_from "$BASE/tasks/$id/edit" /tmp/t_cleanup.html)
    [ -z "$t" ] && continue                       # already gone
    grep -qF -- "$RUN" /tmp/t_cleanup.html || continue   # never ours, leave alone
    curl -sS -c "$JAR" -b "$JAR" -o /dev/null \
      -X POST "$BASE/tasks/$id" -d "_token=$t" -d "_method=DELETE" --max-time 15
  done
  rm -f "$JAR" /tmp/t_guard.html /tmp/t_cleanup.html
}
trap cleanup EXIT

echo "=== BASELINE ==="
C=$(code "$BASE/tasks" /tmp/t_base.html); check "GET /tasks before changes" "$C" "200"
BEFORE=$(all_count /tmp/t_base.html)
ok "baseline task count: ${BEFORE:-unknown}"

echo "=== CREATE ==="
TOKEN=$(token_from "$BASE/tasks/create" /tmp/t_create.html)
[ -n "$TOKEN" ] && ok "CSRF token present on create form" || bad "no CSRF token on create form"

STATUS=$(curl -sS -c "$JAR" -b "$JAR" -o /tmp/t_post.html -w '%{http_code}' \
  -X POST "$BASE/tasks" \
  -d "_token=$TOKEN" \
  --data-urlencode "title=$T1" \
  --data-urlencode "notes=Semi-skimmed, free range" \
  -d "due_date=2026-08-05" -d "priority=high" --max-time 15)
check "POST /tasks returns redirect" "$STATUS" "302"

LOC=$(curl -sS -c "$JAR" -b "$JAR" -o /dev/null -D - -X POST "$BASE/tasks" \
  -d "_token=$TOKEN" --data-urlencode "title=$T2" \
  -d "priority=low" -d "due_date=2026-12-01" --max-time 15 | grep -i '^location' | tr -d '\r')
ok "second task posted (${LOC# })"

echo "=== READ (list) ==="
C=$(code "$BASE/tasks?filter=all" /tmp/t_list.html); check "GET /tasks" "$C" "200"
contains "created task appears in list" "$T1" /tmp/t_list.html
contains "second task appears in list" "$T2" /tmp/t_list.html
contains "flash message shown after redirect" "Added" /tmp/t_list.html
contains "priority badge rendered" "badge-high" /tmp/t_list.html

AFTER=$(all_count /tmp/t_list.html)
check "count rose by the two tasks created" "$AFTER" "$((BEFORE + 2))"

# Resolve ids from the rows carrying THIS run's marker.
ID=$(row_id /tmp/t_list.html "$T1")
ID2=$(row_id /tmp/t_list.html "$T2")
[ -n "$ID" ]  && ok "resolved own task id=$ID from its own row"  || bad "could not resolve task id for '$T1'"
[ -n "$ID2" ] && ok "resolved own second task id=$ID2"           || bad "could not resolve task id for '$T2'"

echo "=== READ (detail) ==="
C=$(code "$BASE/tasks/$ID" /tmp/t_show.html); check "GET /tasks/$ID" "$C" "200"
contains "detail shows notes" "Semi-skimmed, free range" /tmp/t_show.html
contains "detail shows Open status" "badge-open" /tmp/t_show.html

echo "=== UPDATE ==="
if assert_ours "$ID" "update"; then
  TOKEN=$(token_from "$BASE/tasks/$ID/edit" /tmp/t_edit.html)
  contains "edit form prefilled with title" "value=\"$T1\"" /tmp/t_edit.html
  contains "edit form prefilled with due date" 'value="2026-08-05"' /tmp/t_edit.html

  STATUS=$(curl -sS -c "$JAR" -b "$JAR" -o /dev/null -w '%{http_code}' \
    -X POST "$BASE/tasks/$ID" \
    -d "_token=$TOKEN" -d "_method=PUT" \
    --data-urlencode "title=$T1_EDITED" \
    --data-urlencode "notes=Changed my mind" \
    -d "due_date=2026-09-09" -d "priority=medium" --max-time 15)
  check "PUT /tasks/$ID returns redirect" "$STATUS" "302"

  C=$(code "$BASE/tasks/$ID" /tmp/t_show2.html)
  contains "updated title persisted" "$T1_EDITED" /tmp/t_show2.html
  contains "updated notes persisted" "Changed my mind" /tmp/t_show2.html
  absent   "old title gone" "$T1" /tmp/t_show2.html
fi

echo "=== TOGGLE ==="
if assert_ours "$ID" "toggle"; then
  TOKEN=$(token_from "$BASE/tasks" /tmp/t_list2.html)
  STATUS=$(curl -sS -c "$JAR" -b "$JAR" -o /dev/null -w '%{http_code}' \
    -X POST "$BASE/tasks/$ID/toggle" -d "_token=$TOKEN" -d "_method=PATCH" --max-time 15)
  check "PATCH toggle returns redirect" "$STATUS" "302"

  C=$(code "$BASE/tasks/$ID" /tmp/t_show3.html)
  contains "task now marked completed" "badge-done" /tmp/t_show3.html

  C=$(code "$BASE/tasks?filter=done" /tmp/t_done.html)
  contains "task appears under done filter" "$T1_EDITED" /tmp/t_done.html
  C=$(code "$BASE/tasks?filter=active" /tmp/t_active.html)
  absent_row "task gone from active filter" "$T1_EDITED" /tmp/t_active.html
fi

echo "=== VALIDATION (should reject) ==="
TOKEN=$(token_from "$BASE/tasks/create" /tmp/t_create2.html)
STATUS=$(curl -sS -c "$JAR" -b "$JAR" -o /dev/null -w '%{http_code}' \
  -X POST "$BASE/tasks" -d "_token=$TOKEN" -d "title=" -d "priority=urgent" --max-time 15)
check "POST with bad data redirects back" "$STATUS" "302"
C=$(code "$BASE/tasks/create" /tmp/t_errs.html)
contains "title error surfaced" "Please give the task a title." /tmp/t_errs.html
contains "priority error surfaced" "Pick a priority of high, medium or low." /tmp/t_errs.html

echo "=== CSRF enforcement ==="
STATUS=$(curl -sS -o /dev/null -w '%{http_code}' -X POST "$BASE/tasks" \
  -d "title=no token" -d "priority=low" --max-time 15)
check "POST without CSRF token rejected" "$STATUS" "419"

echo "=== 404 handling ==="
STATUS=$(curl -sS -o /dev/null -w '%{http_code}' "$BASE/tasks/999999" --max-time 15)
check "GET missing task 404s" "$STATUS" "404"

echo "=== DELETE ==="
if assert_ours "$ID" "delete"; then
  TOKEN=$(token_from "$BASE/tasks/$ID/edit" /tmp/t_edit2.html)
  STATUS=$(curl -sS -c "$JAR" -b "$JAR" -o /dev/null -w '%{http_code}' \
    -X POST "$BASE/tasks/$ID" -d "_token=$TOKEN" -d "_method=DELETE" --max-time 15)
  check "DELETE /tasks/$ID returns redirect" "$STATUS" "302"

  C=$(code "$BASE/tasks?filter=all" /tmp/t_list3.html)
  absent_row "deleted task gone from list" "$T1_EDITED" /tmp/t_list3.html
  contains "other task survived" "$T2" /tmp/t_list3.html
  STATUS=$(curl -sS -o /dev/null -w '%{http_code}' "$BASE/tasks/$ID" --max-time 15)
  check "deleted task 404s on show" "$STATUS" "404"
fi

echo "=== CLEANUP ==="
cleanup
trap - EXIT
C=$(code "$BASE/tasks?filter=all" /tmp/t_final.html)
FINAL=$(all_count /tmp/t_final.html)
check "task count back to baseline" "$FINAL" "$BEFORE"
absent_row "no test rows left behind" "$RUN" /tmp/t_final.html

echo
echo "==================================="
echo "  PASS: $PASS    FAIL: $FAIL"
echo "==================================="
[ "$FAIL" -eq 0 ]
