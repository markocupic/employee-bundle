# Upgrade 2.* to 3.*
Die Erweiterung wurde komplett überarbeitet und ist nicht mehr kompatibel zur 2.* Version.
Mehrere Felder wurden umbenannt, andere sind dazu gekommen und auch die Templates wurden komplett überarbitet.

## Umbenennung von Tabellenfeldern:
- tl_employee.funktion -> tl_employee.role
- tl_employee.description -> tl_employee.roleDetails
- tl_employee.interview.interview_question -> tl_employee.intetview.question
- tl_employee.interview.interview_answer -> tl_employee.interview.answer
- tl_employee.businessHours.businessHoursWeekday -> tl_employee.businessHours.weekday
- tl_employee.businessHours.businessHoursTime -> tl_employee.businessHours.time

## Neue Felder:
- tl_employee.linkedIn
- tl_employee.xing
- tl_employee.multiSRC

## Frontend Module anstatt Inhaltselemente
Die beiden Inhaltselemente **Mitglieder-Auflistung** und **Mitglieder-Reader** wurden in Frontend Module umgewandelt.

## Totale Überarbeitung der Templates
Die alten Templates können nicht mehr benötigt werden und wurden komplett überarbeitet.
Anstelle von HTML5 sind die beiden Templates in **TWIG** geschrieben.

## Ein Einzelbild und eine Galerie pro Mitarbiter
Pro Mitarbeiter können neben dem Einzelbild auch eine Galerie verwendet werden.
