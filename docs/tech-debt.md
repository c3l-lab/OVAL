# Technical Debt

- Bootstrap is outdated. We are using Bootstrap 3.3.7, which is no longer supported. We should upgrade to Bootstrap 5.
- Routes and controllers are not well organized. Most of them have been refactored, but some still need improvement. The goal is to break up the AjaxController into multiple controllers.
- `api_token` is useless. We should remove it after we get rid of the AjaxController.
