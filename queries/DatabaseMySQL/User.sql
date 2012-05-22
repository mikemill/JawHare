/* [load_user] begin */
SELECT id_user, username, fullname, passwd, email, admin
FROM users
WHERE id_user = {int:id}
/* [load_user] end */
