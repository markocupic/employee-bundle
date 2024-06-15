![Alt text](docs/logo.png?raw=true "logo")

# Employee Bundle

This extension for **Contao CMS** enables the presentation of **employees** on a **company website**.
Employees can be entered via the **backend module**. The extension offers a **list module** and a **reader/detail module** for output in the frontend.

## Backend

![Alt text](docs/tl_employee.png?raw=true "Backend")

## Insert Tags

It is possible to retrieve details or the individual picture of a specific employee via **Contao InsertTag** in **TWIG**.

```html
<div>Title: {{ insert_tags('employee::##alias##::##fieldname##') }}</div>
```
### Examples
```html
{# templates/rsce_employee_detail.html.twig #}

<div>Firstname: {{ insert_tag('employee::adam-riese::firstname') }}</div>
<div>Lastname: {{ insert_tag('employee::adam-riese::lastname') }}</div>
<div>Image: {{ insert_tag('employee::adam-riese::image::mode=proportional&width=200')|raw }}</div>
<div>Picture: {{ insert_tag('employee::adam-riese::picture::size=2')|raw }}</div>
<div>Figure: {{ insert_tag('employee::adam-riese::figure::size=2')|raw }}</div>

{# dynamic #}
<div>Firstname:   {{ insert_tag('employee::'~alias~'::firstname') }}</div>
<div>Figure:   {{ insert_tag('employee::'~alias~'::figure::size='~picture_size) }}</div>

{# VCard download link using the alias or ID #}
<div><a href="{{ insert_tag('employee_vcard_download_url::adam-riese') }}" title="vcard">download VCard</a></div>
<div><a href="{{ insert_tag('employee_vcard_download_url::1') }}" title="vcard">download VCard</a></div>
```

## Events for developers
The two events `GenerateVCardEvent` and `PrepareEmployeeDataEvent` give you control over the output before the templates are rendered.
