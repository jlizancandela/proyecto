todo:

- pasar a npm con inportmap
- crear los webcomponents

# elementos a convertir en webcomponents:

Here are the JavaScript files containing alerts (both native alert()/confirm() and Bootstrap alert components), categorized by their usage:

Admin Panel Scripts (public/js/admin)
These files rely on native alert() for error handling and confirm() for delete confirmations. They also dynamically create Bootstrap alert elements for success messages.

public/js/admin/bookings/bookingsManager.js:
Native: alert on API errors, confirm before deleting.
Bootstrap: showSuccess() helper creates <div class="alert alert-success">.
public/js/admin/usuarios/usersManager.js (Likely similar pattern)
public/js/admin/servicios/servicesManager.js (Likely similar pattern)
User/Client App Scripts (public/js/user)
The client app (built with Preact) uses components to render Bootstrap alerts rather than native browser dialogs.

public/js/user/bookings/components/StatusAlert.js:
Component: A Preact component specifically for rendering alerts.
Bootstrap: Renders <div class="alert alert-info"> (loading) and <div class="alert alert-danger"> (errors).
public/js/user/bookings/bookingsApp.js: Likely imports and uses StatusAlert.
