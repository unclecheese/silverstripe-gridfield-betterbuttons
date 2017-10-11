Better Buttons for GridField
====================================

![Screenshot](http://i.cubeupload.com/J8vWQf.png)


#### Modifies the detail form of GridFields to use more user-friendly actions, including:

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


#### Create custom actions the detail view

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
    public function getBetterButtonsActions() {
        $fields = parent::getBetterButtonsActions();
        if($this->IsApproved) {
            $fields->push(BetterButtonCustomAction::create('deny', 'Deny'));
        }
        else {
            $fields->push(BetterButtonCustomAction::create('approve', 'Approve'));
        }
        return $fields;
    }
```

The ```BetterButtonCustomAction``` object takes parameters for the method name ("deny" or "approve") to invoke on the model, as well as a label for the button.

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
Let's ensure that the form refreshes after clicking "approve" or "deny".

```php
  $fields->push(
    BetterButtonCustomAction::create('deny', 'Deny')
      ->setRedirectType(BetterButtonCustomAction::REFRESH)
  );
```

The redirect type can use the constants:
```php
BetterButtonCustomAction::REFRESH
BetterButtonCustomAction::GOBACK
```
To refresh the form, or go back to list view, respectively.

Additionally, we can add a success message that will render on completion of the action by returning a message in our method.

```php
    public function deny() {
        $this->IsApproved = false;
        $this->write();

        return 'Denied for publication';
    }
```

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


### Creating nested forms

You may have an action that needs to prompt for user input, for example "Send this customer message" on an Order record. For complex actions like these, you can use `BetterButtonNestedForm`.

```php
	public function getBetterButtonsActions() {
		$f = parent::getBetterButtonsActions();
		$f->push(BetterButtonNestedForm::create('sendmessage','Send this customer a message', FieldList::create(
			TextareaField::create('Content')
		)));

		return $f;
	}
```

In this case, your action handler receives `$data` and `$form`, just like a controller would.

```php
    public function sendmessage ($data, $form) {
    	$message = Message::create(array (
    		'OrderID' => $this->ID,
    		'Content' => $data['Content']
    	));

    	$message->write();
    	$form->sessionMessage('Message sent','good');
    }
```

### Implementing With DataExtension

In cases where you may need to implement custom buttons with a `DataExtension` use the extension points provided. These include the following:

- `updateBetterButtonsActions` (`getBetterButtonsActions`)
- `updateBetterButtonsUtils` (`getBetterButtonsUtils`)

The `FieldList` of actions is passed in to these methods. The following example shows how this might work:

```php
    /**
     * @param $actions
     */
    public function updateBetterButtonsActions($actions)
    {
        $actions->push(BetterButtonCustomAction::create('approveRecord', 'Approve Record'));
        $actions->push(BetterButtonNestedForm::create(
            'updateRecord',
            'Update This Record',
            FieldList::create(
                TextareaField::create('FooField')
                    ->setTitle('A Field To Process'),
                HiddenField::create('AHiddenField')
                    ->setValue('A Hidden Value)
            )
        ));
    }
```


### Disabling Better Buttons

Sometimes you might find it necessary to disable better buttons on certain classes. You can do this by changing the static `better_buttons_enabled` to be false via YML configuration.

```yml
MyBetterButtonLessClass
  better_buttons_enabled: false
```
