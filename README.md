# WW Forms

ACF Pro form builder

## Form Fields

- Text Field
- Textarea
- Dropdown
- Checkbox
- Radio Buttons
- Multiple Choice
- Email
- Heading

## Features

- Automatic installation of ACF Field Groups, Post Type, and Option Page
- Paginated submissions table on each form
- Custom submit messages and submit button text
- Sorting by date submitted
- Downloading responses as CSV
- Google Recaptcha

## Installing

To install update your composer.json witht the folling lines:

- Add `"walltowall/ww-forms": "dev-main"` to the require section
- Add the following to the repositories section:

```json
{
  "type": "vcs",
  "url": "git@github.com:WalltoWall/ww-forms.git"
}
```

- Update the muplugins section of installer paths with `"walltowall/ww-forms"`

## Local Development

To start the wordpress docker instance and asset compilation:

```bash
make up
```

To stop and remove the docker containers:

```bash
make down
```

To create a build locally for testing in a project:

```bash
make
```
