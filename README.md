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
    Button_Save: true
    Button_SaveAndAdd: true
    Button_SaveAndClose: true
    Button_Cancel: true
  edit:
    Button_Save: true
    Group_SaveAnd: true
    Button_Cancel: true
    Button_Delete: true

BetterButtonsGroups:
  SaveAnd:
    label: Save and...
    buttons:
      Button_SaveAndAdd: true
      Button_SaveAndClose: true
      Button_SaveAndNext: true
      Button_SaveAndPrev: true


```


Each button type is assigned a symbol in the YAML definition. It can be placed anywhere any number of times. Further, it can be placed in a named group, provided that group has been defined in the BetterButtonsGroups node. A button group is a single button with a label that exposes a series of options on click.

Because of the idiosyncracies of the Config layer merging arrays, the buttons must be defined as on or off (true or false). To remove a button from the default configuration, you must explicitly set it to false in your project configuration. Here is an example custom configuration.

```
BetterButtonsViews:
  edit:
    Button_Save: false
    Group_SaveAnd: false
    Group_MyGroup: true
BetterButtonsGroups:
  MyGroup:
    label: This is a group
    buttons:
      Button_Save: true
      Button_SaveAndNext: true
      
```

When creating groups, be sure not to duplicate any buttons that are outside the group, as form fields with the same name cannot appear twice in a form.

## Todo

Integrate with versioned dataobjects.
