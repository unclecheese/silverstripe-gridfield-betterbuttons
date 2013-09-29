Better Buttons for GridField
====================================

![Screenshot](http://dashboard.unclecheeseproductions.com/mysite/images/better_buttons2.png)


Modifies the detail form of GridFields to use more user-friendly actions, including:

* **Save and add another**: Create a record, and go right to adding another one, without having to click the back button, and then add again.
* **Save and close**: Save the record and go back to list view
* **User-friendly delete**: Extracted from the tray of constructive actions and moved away so is less likely to be clicked accidentally. Includes inline confirmation of action instead of browser alert box.
* **Cancel**: Same as the back button, but in a more convenient location
* **Previous/Next record**: Navigate to the previous or next record in the list without returning to list view
* **Frontend Links**: If your DataObject has a Link() method, get links to the draft site and published site to view the record in context in a single click
* **Versioning**: Save, Save & Publish, Rollback, Unpublish
* **Configurable UI**: Add buttons to the top (utilities) or bottom (actions).
* **Disambiguated tabs**: In model admin, the top tabs toggle between the models. On the detail view, they toggle between the groups of fields, creating a confusing user exierience. Better Buttons groups the fields as they are in CMSMain, using a tabset within the main editing area.


## Requirements
SilverStripe 3.0 or higher

## Customising the button collections

Preferences for which buttons should appear where will vary from user to user. BetterButtons comes with a default set of button collections for the "create" and "edit" views in a GridField detail form, but these can be easily overridden in a config.yml file.

The default configuration:
```
BetterButtonsUtils:
  edit:
    Button_PrevNext: true
    Button_New: true

BetterButtonsActions:
  create:
    Button_Save: true
    Button_SaveAndClose: true

  edit:
    Button_Save: true
    Button_SaveAndClose: true
    Button_Delete: true
    Button_FrontendLinks: true

  versioned_create:
    Button_SaveDraft: true
    Button_Publish: true
  versioned_edit:
    Button_SaveDraft: true
    Button_Publish: true
    Group_Versioning: true
    Button_FrontendLinks: true
    Button_Delete: true
    Button_FrontendLinks: true

BetterButtonsGroups:
  SaveAnd:
    label: Save and...
    buttons:
      Button_SaveAndAdd: true
      Button_SaveAndClose: true
      Button_SaveAndNext: true
      Button_SaveAndPrev: true
  Versioning:
    label: Versioning...
    buttons:
      Button_Rollback: true
      Button_Unpublish: true

```


Each button type is assigned a symbol in the YAML definition. It can be placed anywhere any number of times. Further, it can be placed in a named group, provided that group has been defined in the BetterButtonsGroups node. A button group is a single button with a label that exposes a series of options on click.

Because of the idiosyncracies of the Config layer merging arrays, the buttons must be defined as on or off (true or false). To remove a button from the default configuration, you must explicitly set it to false in your project configuration. Here is an example custom configuration.

```
BetterButtonsActions:
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

