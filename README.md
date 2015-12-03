Flowpack.Neos.FrontendLogin
===========================

TYPO3 Neos plugin demonstrating a simple "frontend login"

DISCLAIMER:
-----------

This is just a basic *prototype* with various limitation. You should think twice before using it in productive applications.
The good news is: We're working hard on improving support for Frontend-Logins.

How-To:
-------

* Install the package to ``Packages/Plugin/Flowpack.Neos.FrontendLogin`` (e.g. via ``composer require flowpack/neos-frontendlogin:dev-master``)
* Run database migrations: ``./flow doctrine:migrate``
* Login to the TYPO3 Neos backend and create a new page "Login" (e.g. at ``/login``)
* On that page insert the new plugin ``Frontend login form``
* Create a page "User Profile" (e.g. at ``/members/profile``)
* On that page insert the plugin ``Frontend user profile``
* Publish all changes
* Create a new Frontend User (you can use the ``frontenduser:create`` command, e.g. ``./flow frontenduser:create user password Your Name``)

Now you should be able to test the frontend login by navigating to ``/login.html``

Hide
----------------

If you want to create a "member area" that is only visible to authenticated frontend users, add the following ``Policy.yaml`` to your site package:

```yaml
privilegeTargets:

  'TYPO3\TYPO3CR\Security\Authorization\Privilege\Node\ReadNodePrivilege':

    'Acme.YourPackage:MembersArea':
      matcher: 'isDescendantNodeOf("/sites/yoursite/some/path")'

  'TYPO3\TYPO3CR\Security\Authorization\Privilege\Node\ReadNodePrivilege':

    'Acme.YourPackage:RestrictNode':
      matcher: 'nodePropertyIs("restrictNode", true) || parentNodePropertyIs("restrictNode", true)'

roles:

  'Flowpack.Neos.FrontendLogin:User':
    privileges:
      -
          # Grant "frontend users" access to the "Member area"
        privilegeTarget: 'Acme.YourPackage:MembersArea'
        permission: GRANT
      -
          # Grant "frontend users" access to Nodes with property "restricted" == true
        privilegeTarget: 'Acme.YourPackage:RestrictNode'
        permission: GRANT

  'TYPO3.Neos:Editor':
    privileges:
      -
          # Grant "backend users" to access the "Member area" - Otherwise those pages would be hidden in the backend, too!
        privilegeTarget: 'Acme.YourPackage:MembersArea'
        permission: GRANT
      -
          # Grant "frontend users" access to Nodes with property "restricted" == true
        privilegeTarget: 'Acme.YourPackage:RestrictNode'
        permission: GRANT
```

**Note:** Replace "Acme.YourPackage" with the package key of your site package and replace "/sites/yoursite/some/path" with the absolute node path of the "member area". The specified node and all its child-nodes will be hidden from anonymous users!

Rewriting the template path to your package:
--------------------------------------------

You might want to modify the template(s) according to your needs. Create a ``Views.yaml`` file and
add the following configuration there:

```yaml
-
  requestFilter: 'isPackage("Flowpack.Neos.FrontendLogin") && isController("Login") && isAction("index")'
  options:
    templatePathAndFilename: 'resource://Acme.YourPackage/Private/Templates/Login/Index.html'
```

Adjust the actual value in ``templatePathAndFilename`` to your needs. The same procedure would go
to the ``Profile`` action, just add another such configuration with ``isAction("index")``.

Known issues:
-------------

* If you use the ``ReadNodePrivilege`` to protect the "member area" and navigate to a protected page Neos issues a 500 error instead of 404
