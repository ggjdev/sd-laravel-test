# Notes Application

A simple application, allowing for registration and logging in of users. Allowing them to store simple text notes no the system, and categorise them.

Currently the system allows users to store Notes, but not to add any personalised categories to the system, or to add them to the Note.

All Notes and Categories must only be visible to the user who created them.

A Note can comprise of the following:

- Title (required)
- Content (not required)
- Categories (none/multiple)

A Category can comprise of the following:

- Name

By running this command, you will migrate and populate the database with one test user (email: test@test.com, password: 123456), along with three Notes for the user.

```
php artisan migrate:fresh --seed
```


# Tasks

- There is currently a bug with saving a Note if it has no content. This needs to be fixed.
- Create the ability for a user to add/edit/view Categories to the system.
- Modify Notes to allow the user to assign any number of their own Categories to the Note.

### You do not need to worry about how it looks, focus on functionality.