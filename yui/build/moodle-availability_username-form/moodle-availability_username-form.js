YUI.add('moodle-availability_username-form', function (Y, NAME) {

/**
 * JavaScript for form editing profile conditions.
 *
 * @module moodle-availability_username-form
 */
M.availability_username = M.availability_username || {};

/**
 * @class M.availability_username.form
 * @extends M.core_availability.plugin
 */
M.availability_username.form = Y.Object(M.core_availability.plugin);

/**
 * Groupings available for selection (alphabetical order).
 *
 * @property profiles
 * @type Array
 */
M.availability_username.form.profiles = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} standardFields Array of objects with .field, .display
 * @param {Array} customFields Array of objects with .field, .display
 */
M.availability_username.form.initInner = function() {
};

M.availability_username.form.getNode = function(json) {
    // Create HTML structure.
    var strings = M.str.availability_username;
    var html = '<span class="availability-group"> <label>' + strings.label_username +
            ' <input name="username" type="text" style="width: 10em" title="' +
            strings.title_username + '"/></label></span>';
    var node = Y.Node.create('<span>' + html + '</span>');

	console.log('json',json);
    if (json.v !== undefined) {
        node.one('input').set('value', json.v);
    }

    // Add event handlers (first time only). You can do this any way you
    // like, but this pattern is used by the existing code.
    if (!M.availability_username.form.addedEvents) {
        M.availability_username.form.addedEvents = true;
        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('change', function() {
            // The key point is this update call. This call will update
            // the JSON data in the hidden field in the form, so that it
            // includes the new value of the checkbox.
            M.core_availability.form.update();
        }, '.availability_username input');
    }

    return node;
};

M.availability_username.form.fillValue = function(value, node) {
    var valueNode = node.one('input[name=username]');
	value.v = valueNode.get('value');
	console.log('fillValue',value.v);
};

M.availability_username.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);
	console.log('fillErrors',value.v);
    if (value.v !== undefined && /^\s*$/.test(value.v)) {
        errors.push('availability_username:error_setusername');
    }
};


}, '@VERSION@', {"requires": ["base", "node", "event", "moodle-core_availability-form"]});
