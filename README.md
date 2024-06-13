![Alt text](docs/logo.png?raw=true "logo")

# Employee Bundle

Diese Erweiterung für **Contao CMS** ermöglicht die Abbildung von **Mitarbeitern** auf einer **Firmenwebseite**.
Über das **Backendmodul** können Mitarbeiter erfasst werden. Zur Ausgabe im Frontend bietet die Extension ein **Listenmodul** sowie ein **Reader-/Detailmodul**.

## Backend

![Alt text](docs/tl_employee.png?raw=true "Backend")

## Insert Tags

Es besteht die Möglichkeit Angaben oder das Einzelbild eines bestimmten Mitarbeiters via **Contao InsertTag** im **TWIG** abzurufen.

`<div>Firstname: {{ insert_tags('employee::##alias##::##feldname##') }}</div>`

### Beispiele

```
{# templates/rsce_employee_detail.html.twig #}

<div>Firstname: {{ insert_tag('employee::adam-riese::firstname') }}</div>
<div>Lastname: {{ insert_tag('employee::adam-riese::lastname') }}</div>
<div>Image: {{ insert_tag('employee::adam-riese::image::mode=proportional&width=200')|raw }}</div>
<div>Picture: {{ insert_tag('employee::adam-riese::picture::size=2')|raw }}</div>
<div>Figure: {{ insert_tag('employee::adam-riese::figure::size=2')|raw }}</div>

{# VCard Link mit Alias#}
<div><a href="{{ insert_tag('employee_vcard_download_url::adam-riese') }}" title="vcard">VCard herunterladen</a></div>

{# VCard Link mit ID#}
<div><a href="{{ insert_tag('employee_vcard_download_url::1') }}" title="vcard">VCard herunterladen</a></div>

{# Dynamisch #}
<div>Firstname:   {{ insert_tag('employee::'~alias~'::firstname') }}</div>
<div>Figure:   {{ insert_tag('employee::'~alias~'::figure::size='~picture_size) }}</div>
```
