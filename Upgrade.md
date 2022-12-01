# Upgrade 2.* to 3.*
Die Erweiterung wurde komplett überarbeitet und ist nicht mehr kompatibel zur 2.* Version.
Mehrere Felder wurden umbenannt, andere sind dazugekommen und auch die Templates wurden komplett überarbeitet.
Beide Inhaltselemente wurden durch Frontendmodule ersetzt.

## Umbenennung von Tabellenfeldern:
- tl_employee.funktion -> tl_employee.role
- tl_employee.description -> tl_employee.roleDetails
- tl_employee.interview.interview_question -> tl_employee.intetview.question
- tl_employee.interview.interview_answer -> tl_employee.interview.answer
- tl_employee.businessHours.businessHoursWeekday -> tl_employee.businessHours.weekday
- tl_employee.businessHours.businessHoursTime -> tl_employee.businessHours.time

Bei der Datenbankmigration werden die Daten automatisch in die neuen Felder migriert.

## Neue Felder:
- tl_employee.linkedIn
- tl_employee.xing
- tl_employee.multiSRC
- tl_module.addEmployeeImage
- tl_module.addEmployeeGallery
- tl_module.addEmployeeGallery
- tl_module.imgSize
- tl_module.galSize
- tl_module.imgFullsize
- tl_module.galFullsize
- tl_module.addEmployeeImage
- tl_module.addEmployeeImage
- tl_module.addEmployeeImage

## Frontend Module anstatt Inhaltselemente
Die beiden Inhaltselemente **Mitglieder-Auflistung** und **Mitglieder-Reader** wurden in Frontend Module umgewandelt.
!Achtung: Es werden diesbezüglich keine Daten migriert. Die Frontend Module müssen von Hand angelegt werden.

## Totale Überarbeitung der Templates
Die alten Templates können nicht mehr benötigt werden und wurden komplett überarbeitet.
Anstelle von HTML5 sind die beiden Templates in **TWIG** geschrieben.

## Ein Einzelbild und eine Galerie pro Mitarbeiter
Pro Mitarbeiter können neben dem Einzelbild auch eine Galerie verwendet werden.
