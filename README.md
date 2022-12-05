![Alt text](docs/logo.png?raw=true "logo")

# Employee Bundle

Diese Erweiterung für **Contao CMS** ermöglicht die Abbildung von **Mitarbeitern** auf einer **Firmenwebseite**.
Über das **Backendmodul** können Mitarbeiter erfasst werden. Zur Ausgabe im Frontend bietet die Extension ein **Listenmodul** sowie ein **Reader-/Detailmodul**.

## Backend
![Alt text](docs/tl_employee.png?raw=true "Backend")


## Insert Tags
Es besteht die Möglichkeit Angaben oder das Einzelbild eines bestimmten Mitarbeiters via **Contao InsertTag** im **TWIG** oder **HTML5 Template** abzurufen.

`{{employee::##emloyeeIdOrAlias##::##strField##}}`

Insert Tags in **TWIG** templates:

```
{# templates/rsce_employee_detail.html.twig #}

<div>Firstname: {{ '{{employee::adam-riese::firstname}}' }}</div>
<div>Lastname: {{ '{{employee::adam-riese::firstname}}' }}</div>
<div>Image: {{ '{{employee::adam-riese::image::mode=proportional&width=200}}' }}</div>
<div>Picture: {{ '{{employee::adam-riese::picture::size=2}}' }}</div>
<div>Figure: {{ '{{employee::adam-riese::figure::size=2}}' }}</div>

{# Dynamisch #}
<div>Firstname:   {{ '{{employee::'~alias~'::firstname}}' }}</div>
<div>Figure:   {{ '{{employee::'~alias~'::figure::size='~picture_size~'}}' }}</div>
```

Insert Tags in **HTML5** templates:

```
<!-- templates/rsce_employee_detail.html5 -->

<div>Firstname: {{employee::adam-riese::firstname}}</div>
<div>Lastname: {{employee::adam-riese::firstname}}</div>
<div>Image: {{employee::adam-riese::image::mode=proportional&width=200}}</div>
<div>Picture: {{employee::adam-riese::picture::size=2}}</div>
<div>Figure: {{employee::adam-riese::figure::size=2}}</div>

<!-- Dynamisch -->
<div>Firstname: {{employee::<?= $this->alias ?>::firstname}}</div>
<div>Figure: {{employee::<?= $this->alias ?>::figure::size=<?= $this->picture_size ?>}}</div>
```
