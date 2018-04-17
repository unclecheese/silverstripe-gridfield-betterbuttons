# Much of this module is getting merged into core

https://github.com/silverstripe/silverstripe-admin/issues/436

## I want to use it now!

If you're looking to use it in SS4 ahead of the merge, you can use this branch.

## Bigest change: New config API

Rough outline:

```yaml
UncleCheese\BetterButtons\Extensions\BetterButtons:
  utils:
    edit:
      prevNext: true
      create: true
    versioned_edit:
      prevNext: true
      create: true
    create:
      create: true
  actions:
    create:
      save: true
      saveAndClose: true
    edit:
      save: true
      saveAndClose: true
      delete: true
      frontendLinks: true
    versioned_edit:
      group:
        label: Versioning...
        buttons:
          rollback: true
      frontendLinks: true
```      
