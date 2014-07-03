Better Buttons for GridField
====================================

![Screenshot](http://i.cubeupload.com/J8vWQf.png)


### Modifies the detail form of GridFields to use more user-friendly actions, including:

* **Save and add another**: Create a record, and go right to adding another one, without having to click the back button, and then add again.
* **Save and close**: Save the record and go back to list view
* **User-friendly delete**: Extracted from the tray of constructive actions and moved away so is less likely to be clicked accidentally. Includes inline confirmation of action instead of browser alert box.
* ![Screenshot](http://i.cubeupload.com/TeqGVu.png)
* **Cancel**: Same as the back button, but in a more convenient location
* **Previous/Next record**: Navigate to the previous or next record in the list without returning to list view
* **Frontend Links**: If your DataObject has a Link() method, get links to the draft site and published site to view the record in context in a single click
* ![Screenshot](http://i.cubeupload.com/7YIYv9.png)
* **Versioning**: Save, Save & Publish, Rollback, Unpublish
* ![Screenshot](http://i.cubeupload.com/XJnsMq.png)
* **Configurable UI**: Add buttons to the top (utilities) or bottom (actions).
* **Disambiguated tabs**: In model admin, the top tabs toggle between the models. On the detail view, they toggle between the groups of fields, creating a confusing user exierience. Better Buttons groups the fields as they are in CMSMain, using a tabset within the main editing area.
* ![Screenshot](http://i.cubeupload.com/oFMGbX.png)
* Add your own custom actions!


### Create custom actions the detail view

![Screenshot](http://i.cubeupload.com/QQL8oD.png)

## Requirements
SilverStripe 3.1 or higher

## Installation
```composer require unclecheese/betterbuttons:1.2.*```

## Customising the button collections

Preferences for which buttons should appear where will vary from user to user. BetterButtons comes with a default set of button collections for the "create" and "edit" views in a GridField detail form, but these can be easily overridden in a config.yml file.

The default configuration:
```
BetterButtonsUtils:
  edit:
    BetterButtonPrevNextAction: true
    BetterButton_New: true
  versioned_edit:
    BetterButtonPrevNextAction: true
    BetterButton_New: true

BetterButtonsActions:
  create:
    BetterButton_Save: true
    BetterButton_SaveAndClose: true

  edit:
    BetterButton_Save: true
    BetterButton_SaveAndClose: true
    BetterButton_Delete: true
    BetterButtonFrontendLinksAction: true

  versioned_create:
    BetterButton_SaveDraft: true
    BetterButton_Publish: true
  versioned_edit:
    BetterButton_SaveDraft: true
    BetterButton_Publish: true
    Group_Versioning: true
    BetterButton_Delete: true
    BetterButtonFrontendLinksAction: true

BetterButtonsGroups:
  SaveAnd:
    label: Save and...
    buttons:
      BetterButton_SaveAndAdd: true
      BetterButton_SaveAndClose: true
      BetterButton_SaveAndNext: true
      BetterButton_SaveAndPrev: true
  Versioning:
    label: Versioning...
    buttons:
      BetterButton_Rollback: true
      BetterButton_Unpublish: true

```


Each button type is assigned a symbol in the YAML definition. It can be placed anywhere any number of times. Further, it can be placed in a named group, provided that group has been defined in the BetterButtonsGroups node. A button group is a single button with a label that exposes a series of options on click.

Because of the idiosyncracies of the Config layer merging arrays, the buttons must be defined as on or off (true or false). To remove a button from the default configuration, you must explicitly set it to false in your project configuration. Here is an example custom configuration.

```
BetterButtonsActions:
  edit:
    BetterButton_Save: false
    Group_SaveAnd: false
    Group_MyGroup: true
BetterButtonsGroups:
  MyGroup:
    label: This is a group
    buttons:
      BetterButton_Save: true
      BetterButton_SaveAndNext: true
      
```

When creating groups, be sure not to duplicate any buttons that are outside the group, as form fields with the same name cannot appear twice in a form.

## Creating a custom action

In the example below, we'll create a custom action in the GridField detail form that updates a DataObject to be "approved" or "denied."

We can add the action in one of two places:
* **Actions** at the bottom of the form (e.g. save, cancel)
* **Utils** at the top right of the form (e.g. new record, prev/next)


First, we'll overload the model's ```getBetterButtonsActions``` or ```getBetterButtonsUtils``` method, depending on where we want the button to appear in the UI.

```php
    public function getBetterButtonsActions($form, $request) {
        $fields = parent::getBetterButtonsActions($form, $request);
        if($this->IsApproved) {
            $fields->push(BetterButtonCustomAction::create('deny', 'Deny', $form, $request));
        }
        else {
            $fields->push(BetterButtonCustomAction::create('approve', 'Approve', $form, $request));
        }
        return $fields;
    }
```

The ```BetterButtonCustomAction``` object takes parameters for the method name ("deny" or "approve") to invoke on the model, as well as a label for the button, and requisite ```$form``` and ```$request``` references.

Now let's add the methods to the DataObject.

```php
    public function approve() {
        $this->IsApproved = true;
        $this->write();
    }

    public function deny() {
        $this->IsApproved = false;
        $this->write();
    }
```

Lastly, for security reasons, we need to whitelist these methods as callable by the GridField form. This works a lot like ```$allowed_actions``` in controllers.

```php
    private static $better_buttons_actions = array (
        'approve',
        'deny'
    );
```

Now we have a new button in the UI!

![Screenshot](http://i.cubeupload.com/hoU66o.png)

### Customising the user experience
Let's ensure that the form refreshes after clicking "approve" or "deny". Additionally, we'll add a success message that will render on completion of the action.

```php
  $fields->push(
    BetterButtonCustomAction::create('deny', 'Deny', $form, $request)
      ->setRedirectType(BetterButtonCustomAction::REFRESH)
      ->setSuccessMessage('Denied for publication')
  );
```

The redirect type can use the constants:
```php
BetterButtonCustomAction::REFRESH
BetterButtonCustomAction::GOBACK
```
To refresh the form, or go back to list view, respectively.

### Defining arbitrary links
Sometimes, you might not want to sent a request to the controller at all. For that, there's the much simpler ```BetterButtonLink``` class.

```php
    $fields->push(
        new BetterButtonLink(
          'View on Meetup.com',
           $this->MeetUpLink
        )
    );
```

![Screenshot](http://i.cubeupload.com/YbbhL7.png)

