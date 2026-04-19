# UOC Football - System Unit Test Cases (84 Cases)

## Scope
This document defines unit/API-focused test cases across all major actions in the system for public users, admin, coach, captain, and player flows.

## Conventions
- ID format: `UT-###`
- Type: `Controller`, `Model`, or `Integration-Unit`
- Expected results include both success and key validation/guard behavior.

## Test Cases

| ID | Module/Action | Type | Preconditions | Test Input / Steps | Expected Result |
|---|---|---|---|---|---|
| UT-001 | `login::index` renders login page | Controller | None | GET login route | HTTP 200, login view rendered |
| UT-002 | `login::authenticate` success for admin | Controller | Valid admin user exists | POST valid credentials | Session created, redirect/admin route returned |
| UT-003 | `login::authenticate` success for coach | Controller | Valid coach user exists | POST valid credentials | Session created, coach dashboard route returned |
| UT-004 | `login::authenticate` success for player | Controller | Valid player user exists | POST valid credentials | Session created, player dashboard route returned |
| UT-005 | `login::authenticate` invalid password | Controller | Valid user exists | POST wrong password | Auth fails, proper error response/message |
| UT-006 | `login::authenticate` unknown user | Controller | None | POST unknown user id | Auth fails, no session created |
| UT-007 | `login::logout` clears session | Controller | Logged-in session | Invoke logout | Session destroyed, redirected to login/landing |
| UT-008 | `Logout::index` route logout behavior | Controller | Logged-in session | GET `/Logout` | Session cleared and redirected |
| UT-009 | `PasswordChange::index` access with `must_change_password` | Controller | Session flagged for password change | GET password change page | Form rendered |
| UT-010 | `PasswordChange::update` valid new password | Controller | Auth user, valid old/new password rules | POST password change | Password updated, flag cleared |
| UT-011 | `PasswordChange::update` rejects weak password | Controller | Auth user | POST weak password | Validation error returned |
| UT-012 | `LandingPage::index` loads latest content blocks | Controller | Seeded news/events/gallery/team | GET landing page | Latest datasets mapped and view rendered |
| UT-013 | `Home::index` default home route | Controller | None | GET `/` or home route | Valid response or redirect to landing |
| UT-014 | `_404::index` handles missing route | Controller | Invalid route requested | GET unknown route | 404 view rendered |
| UT-015 | `news::index` public news listing | Controller | News records exist | GET `/news` | News list rendered |
| UT-016 | `moreNews::index` extended news listing | Controller | News records exist | GET `/moreNews` | Paginated/extended list rendered |
| UT-017 | `moreEvent::index` extended events listing | Controller | Event records exist | GET `/moreEvent` | Paginated/extended events rendered |
| UT-018 | `gallery::index` public gallery listing | Controller | Gallery records exist | GET `/gallery` | Gallery rendered with images |
| UT-019 | `team::index` public team page | Controller | Team/player data exists | GET `/team` | Team details rendered |
| UT-020 | `Store::index` public store listing | Controller | Store items exist | GET `/store` | Public store cards rendered |
| UT-021 | `adminDashboard::index` dashboard data loads | Controller | Admin session | GET `/adminDashboard` | Notices/events loaded in view data |
| UT-022 | `adminDashboard::addNotice` success | Controller | Admin session | POST valid title/content | Notice created, success JSON |
| UT-023 | `adminDashboard::addNotice` invalid payload | Controller | Admin session | POST empty title/content | Validation error JSON |
| UT-024 | `adminDashboard::updateNotice` success | Controller | Existing notice | POST `notice_id`, new title/content | Notice updated, success JSON |
| UT-025 | `adminDashboard::updateNotice` missing notice | Controller | Non-existent ID | POST invalid `notice_id` | Not found JSON |
| UT-026 | `adminDashboard::deleteNotice` success | Controller | Existing active notice | POST `notice_id` | Notice soft-deleted (`is_active=0`) |
| UT-027 | `budgetManagement::index` loads budget screen | Controller | Admin session/team context | GET `/budgetManagement` | Budget summary and records rendered |
| UT-028 | `budgetManagement::add` income/expense add success | Controller | Valid budget context | POST valid item payload | Budget row created |
| UT-029 | `budgetManagement::update` success | Controller | Existing budget row | POST updated payload | Budget row updated |
| UT-030 | `budgetManagement::delete` success | Controller | Existing budget row | POST row id | Row removed/deactivated successfully |
| UT-031 | `budgetManagement::get` fetch specific record | Controller | Existing budget row | GET record id | Correct row JSON returned |
| UT-032 | `eventManagement::index` event management screen | Controller | Admin session | GET `/eventManagement` | Events list rendered |
| UT-033 | `eventManagement::add` success | Controller | Admin session | POST valid event data | Event inserted |
| UT-034 | `eventManagement::update` success | Controller | Existing event | POST updated data | Event updated |
| UT-035 | `eventManagement::delete` success | Controller | Existing event | POST event id | Event removed |
| UT-036 | `eventManagement::get` single event fetch | Controller | Existing event | GET event id | Correct event JSON returned |
| UT-037 | `newsManagement::index` screen loads | Controller | Admin session | GET `/newsManagement` | News management view rendered |
| UT-038 | `newsManagement::add` success | Controller | Admin session | POST valid title/date/content/image | News row created |
| UT-039 | `newsManagement::update` success | Controller | Existing news | POST updated fields | News row updated |
| UT-040 | `newsManagement::delete` success | Controller | Existing news | POST news id | News row deleted |
| UT-041 | `newsManagement::get` single fetch | Controller | Existing news | GET news id | Correct item JSON returned |
| UT-042 | `galleryManagement::index` screen loads | Controller | Admin session | GET `/galleryManagement` | Existing gallery data shown |
| UT-043 | `galleryManagement::uploadMultiple` success | Controller | Valid image files | POST multi-file upload | Files saved, DB rows created |
| UT-044 | `galleryManagement::update` success | Controller | Existing gallery item | POST updated metadata | Row updated |
| UT-045 | `galleryManagement::delete` success | Controller | Existing gallery item | POST item id | Row and file handling successful |
| UT-046 | `teamManagement::index` full team management load | Controller | Admin session | GET `/teamManagement` | Teams/players/coaches loaded |
| UT-047 | `teamManagement::create` team creation success | Controller | Valid season/status | POST new team | Team inserted |
| UT-048 | `teamManagement::create` validation fail duplicate season/status | Controller | Conflicting team state | POST duplicate | Validation error |
| UT-049 | `teamManagement::checkPlayerExists` | Controller | Existing player | POST player identifier | Existence JSON true |
| UT-050 | `teamManagement::linkPlayerToTeam` success | Controller | Existing team + player | POST team_id/player_id | Link row inserted |
| UT-051 | `teamManagement::addPlayerToTeam` success path | Controller | Player + team valid | POST payload | Link + response success |
| UT-052 | `teamManagement::checkCoachExists` | Controller | Existing coach | POST coach identifier | Existence JSON true |
| UT-053 | `teamManagement::linkCoachToTeam` success | Controller | Existing team + coach | POST team_id/coach_id | Link row inserted |
| UT-054 | `teamManagement::addCoachToTeam` success | Controller | Coach + team valid | POST payload | Link + response success |
| UT-055 | `teamManagement::getPlayerData` | Controller | Existing player | GET player id | Player JSON returned |
| UT-056 | `teamManagement::getCoachData` | Controller | Existing coach | GET coach id | Coach JSON returned |
| UT-057 | `teamManagement::updatePlayer` success | Controller | Existing player | POST updated fields | Player row updated |
| UT-058 | `teamManagement::updateCoach` success | Controller | Existing coach | POST updated fields | Coach row updated |
| UT-059 | `teamManagement::getTeamData` | Controller | Existing team | GET team id | Team detail JSON returned |
| UT-060 | `teamManagement::update` team update success | Controller | Existing team | POST season/status edits | Team row updated |
| UT-061 | `teamManagement::addPlayer` creates new player+user | Controller | Unique NIC/user_id | POST player profile | User + player created |
| UT-062 | `teamManagement::addCoach` creates new coach+user | Controller | Unique NIC/user_id | POST coach profile | User + coach created |
| UT-063 | `teamManagement::delete` team delete constraints | Controller | Team with/without links | POST team id | Proper delete or guarded error |
| UT-064 | `teamManagement::deletePlayer` success | Controller | Existing player | POST player id | Player removed with integrity checks |
| UT-065 | `teamManagement::deleteCoach` success | Controller | Existing coach | POST coach id | Coach removed with integrity checks |
| UT-066 | `teamResult::index` result dashboard loads | Controller | Team exists | GET `/teamResult` | Test/match results and stats shown |
| UT-067 | `teamResult::addTestResult` success | Controller | Valid player/team/date | POST test result | Test row inserted |
| UT-068 | `teamResult::editTestResult` success | Controller | Existing test row | POST edited values | Row updated |
| UT-069 | `teamResult::deleteTestResult` success | Controller | Existing test row | POST id | Row deleted |
| UT-070 | `teamResult::addMatchResult` success | Controller | Valid match payload | POST match result | Match row inserted |
| UT-071 | `teamResult::editMatchResult` success | Controller | Existing match row | POST updated values | Match row updated |
| UT-072 | `teamResult::deleteMatchResult` success | Controller | Existing match row | POST id | Match row deleted |
| UT-073 | `teamResult::getTestResult` single fetch | Controller | Existing row | GET id | JSON row returned |
| UT-074 | `teamResult::getMatchResult` single fetch | Controller | Existing row | GET id | JSON row returned |
| UT-075 | `teamResult::getTeamMembers` | Controller | Team exists | GET team id | Team members returned |
| UT-076 | `teamResult::addPlayerMatchStats` success | Controller | Valid match/player | POST stats payload | Stats row inserted |
| UT-077 | `teamResult::getPlayerMatchStats` | Controller | Existing stats | GET filters | Stats dataset returned |
| UT-078 | `teamResult::editPlayerMatchStats` success | Controller | Existing stat row | POST updated fields | Stats row updated |
| UT-079 | `teamResult::deletePlayerMatchStats` success | Controller | Existing stat row | POST id | Row deleted |
| UT-080 | `teamResult::getPlayersForMatch` + `getMatchPlayerStats` | Controller | Existing match | GET match id | Player list and stat summary returned |
| UT-081 | `teamResult::getPlayerStat` + `updatePlayerMatchStats` | Controller | Existing stat row | GET then POST update | Read + update both succeed |
| UT-082 | `InventoryManagement` admin CRUD + snapshot polling | Integration-Unit | Admin session/team items | Test add, update, get, delete, snapshot | Correct stock/status JSON and persistence |
| UT-083 | `CaptainInventory` CRUD + chart + snapshot | Integration-Unit | Captain session/team items | Test store/update/delete/getChartData/snapshot | Team-scoped inventory behaves correctly |
| UT-084 | `PlayerInventory` take/return flow with `inventory_log` | Integration-Unit | Player session, available item | Take item, verify availability reduced; return item, verify restored; check open/history logs | `inventory_items` and `inventory_log` stay consistent |

## Additional Coverage Notes (for full regression cycle)
- Coach modules: `coachDashboard::updateProfile`, `coachAttendance::update/export`, `coachMealplan::data/save/reset`, `coachEvents::index`, `coachPerformance::index` should be executed in role-based smoke tests.
- Captain modules: `CaptainAttendance::update/export`, `CaptainFinance::addIncome/addExpense/update/delete/getChartData`, `CaptainSchedule::index`, `CaptainAnalyze::index`, `CaptainMealPlan::index` should be included in role regression.
- Player modules: `playerDashboard::updateProfile`, `Schedule::index`, `Analyze::index`, `MealPlan::index`, `Notices::index` should be included in player regression.
- Store management: `StoreManagement::addItem/getItem/editItem/deleteItem/updateStatus` should be included in admin store regression.
- Public content pages: `LandingPage`, `news`, `moreNews`, `moreEvent`, `gallery`, `team`, `store` should be included in public smoke/regression packs.

## Suggested Execution Strategy
1. Run UT-001 to UT-020 as public/auth smoke set.
2. Run UT-021 to UT-081 in role-segmented batches (admin, coach, captain, player).
3. Run UT-082 to UT-084 as high-priority integrity tests before release.
4. Add database fixture reset between suites to ensure repeatable results.
