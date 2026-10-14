# Test Report: StudentHub

| ID | Module | Test | Expected | Result |
|---|---|---|---|---|
| T1 | Register | Valid details | Account created, redirect to login | Pass |
| T2 | Register | Duplicate email | "Already registered" error | Pass |
| T3 | Register | Password < 8 chars | Validation error | Pass |
| T4 | Login | Correct credentials + role | Redirect (admin to dashboard) | Pass |
| T5 | Login | Wrong password | "Invalid email or password" | Pass |
| T6 | Login | Wrong role selected | "Account does not have the selected role" | Pass |
| T7 | Login | 5 wrong attempts | Account locked 10 minutes | Pass |
| T8 | Session | Idle > 15 min | Redirect to login with timeout message | Pass |
| T9 | Session | Remember me, close browser, reopen | Still logged in | Pass |
| T10 | Logout | Click Logout, press Back | Redirected to login | Pass |
| T11 | RBAC | Student opens dashboard.php | 403 Access denied | Pass |
| T12 | RBAC | Logged out opens profile.php | Redirect to login | Pass |
| T13 | Students | Add, edit, delete | Success messages shown | Pass |
| T14 | Students | Search and course filter | Correct rows shown | Pass |
| T15 | Events | Upload valid JPG under 2 MB | Event saved with poster | Pass |
| T16 | Events | Upload PDF or file over 2 MB | Red error, nothing saved | Pass |
| T17 | AJAX | Add, edit, delete in students_fetch.php | Table updates, no reload | Pass |
| T18 | API | Open api_students.php while logged out | JSON 401 | Pass |
| T19 | Dashboard | Statistics and latest registrations | Counts match database | Pass |
| T20 | Audit | Login, failed login, logout | Rows appear in audit log | Pass |
| T21 | Security | Enter `<script>alert(1)</script>` as a name | Shown as text, not executed | Pass |
| T22 | Security | Enter `' OR 1=1 --` as the email | Login fails | Pass |
| T23 | Responsive | Phone width (DevTools, 375px) | Menu stacks, no horizontal scroll | Pass |
| T24 | Navigation | Click every menu link | No 404 | Pass |

Change any row to **Fail** (with a note) if it does not behave as expected during your own run. Run every test yourself before submitting.