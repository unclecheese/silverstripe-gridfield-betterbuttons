Better Buttons for GridField
====================================

![Screenshot](http://dashboard.unclecheeseproductions.com/mysite/images/better_buttons2.png)


Modifies the detail form of GridFields to use more user-friendly actions, including:

* **Save and add another**: Create a record, and go right to adding another one, without having to click the back button, and then add again.
* **Save and close**: Save the record and go back to list view
* **User-friendly delete**: Extracted from the tray of constructive actions and moved away so is less likely to be clicked accidentally. Includes inline confirmation of action instead of browser alert box.
* **Cancel**: Same as the back button, but in a more convenient location
* **Previous/Next record**: Navigate to the previous or next record in the list without returning to list view


## Requirements
SilverStripe 3.0 or higher

## Customising the button collections

Preferences for which buttons should appear where will vary from user to user. BetterButtons comes with a default set of button collections for the "create" and "edit" views in a GridField detail form, but these can be easily overridden in a config.yml file.

The default configuration:
```
BetterButtonsViews:
  create:
    - Button_Save
    - Button_SaveAndAdd
    - Button_SaveAndClose
    - Button_Cancel
  edit:
    - Button_Save
    - Group_SaveAnd
    - Button_Cancel
    - Button_Delete

BetterButtonsGroups:
  SaveAnd:
    label: Save and...
    buttons:
      - Button_SaveAndAdd
      - Button_SaveAndClose
      - Button_SaveAndNext
      - Button_SaveAndPrev

```


Each button type is assigned a symbol in the YAML definition. It can be placed anywhere any number of times. Further, it can be placed in a named group, provided that group has been defined in the BetterButtonsGroups node. A button group is a single button with a label that exposes a series of options on click.

## Todo

Integrate with versioned dataobjects.
