export class ZkErrorElement {

    constructor(error, formBuilder = null) {
        this.error = error || null;
        this.formBuilder = formBuilder;
        this.context = document.createDocumentFragment();
        this.init();
    }

    init() {
        if (!this.error && !this.error.errors) return;

        // Start Error Wrapper
        // Before Error
        const wrapper = this.formBuilder.createWrapperElement(this.error.wrapper || [], this.context);

        // Create Error
        for (let error of this.error.errors) {
            const elm = document.createElement(this.error.tag || 'span');
            // Set Attributes
            for (let attr in this.error.attributes) {
                const value = this.error.attributes[attr];
                elm.setAttribute(isNaN(attr) ? attr : value, isNaN(attr) ? value : true);
            }
            // Before Error
            elm.appendChild(this.formBuilder.stringToHTML(this.error.before || ''));
            // Error Text
            elm.appendChild(this.formBuilder.stringToHTML(error));
            // After Error
            elm.appendChild(this.formBuilder.stringToHTML(this.error.after || ''));

            // Append Error to Context
            wrapper.appendChild(elm);
        }
    }

    position() {
        return this.error?.position || 'after-input';
    }

    render(position = 'after-input') {
        return (this.error && this.position() === position) ? this.context : '';
    }
}

export class ZkLabelElement {

    constructor(label, formBuilder = null) {
        this.label = label || null;
        this.formBuilder = formBuilder;
        this.context = document.createDocumentFragment();
        this.init();
    }

    init() {
        if (!this.label) return;

        // Start Label Wrapper
        const wrapper = this.formBuilder.createWrapperElement(this.label.wrapper || [], this.context);
        // Create Label
        const elm = document.createElement(this.label.tag);
        // Set Attributes
        for (let attr in this.label.attributes) {
            const value = this.label.attributes[attr];
            elm.setAttribute(isNaN(attr) ? attr : value, isNaN(attr) ? value : true);
        }
        // Before Label
        elm.appendChild(this.formBuilder.stringToHTML(this.label.before || ''));
        // Label Text
        elm.appendChild(document.createTextNode(this.label.text));
        // After Label
        elm.appendChild(this.formBuilder.stringToHTML(this.label.after || ''));
        // Append Label to Context
        wrapper.appendChild(elm);
    }

    position() {
        return this.label?.position || 'before-input';
    }

    render(position = 'before-input') {
        return (this.label && this.position() === position) ? this.context : '';
    }

}

export class ZkElement {

    constructor(field, formBuilder = null) {
        this.field = field || null;
        this.viewOnly = this.field?.viewOnly || false;
        this.formBuilder = formBuilder;
        this.context = document.createDocumentFragment();
        this.init();
    }

    init() {
        if (!this.field) return;

        const label = this.formBuilder.createElementInstance(this.field.label, this);
        const error = this.formBuilder.createElementInstance(this.field.error, this);
        const addAction = (!this.viewOnly && this.field.type === 'multiple') ? this.formBuilder.createElementInstance(this.field.addAction, this) : null;

        // Label, Error, Action - before-wrapper
        this.formBuilder.renderElementInPosition(this.context, label, 'before-wrapper');
        this.formBuilder.renderElementInPosition(this.context, error, 'before-wrapper');
        this.formBuilder.renderElementInPosition(this.context, addAction, 'before-wrapper');

        // Start Field Wrapper
        const wrapper = this.formBuilder.createWrapperElement(this.field.wrapper || [], this.context);

        // Label, Error, Action - before-input
        this.formBuilder.renderElementInPosition(wrapper, label, 'before-input');
        this.formBuilder.renderElementInPosition(wrapper, error, 'before-input');
        this.formBuilder.renderElementInPosition(wrapper, addAction, 'before');

        // Create Field
        this.buildField(wrapper);

        // Action, Error, Label - after-input
        this.formBuilder.renderElementInPosition(wrapper, addAction, 'after');
        this.formBuilder.renderElementInPosition(wrapper, error, 'after-input');
        this.formBuilder.renderElementInPosition(wrapper, label, 'after-input');

        // Action, Label, Error - after-wrapper
        this.formBuilder.renderElementInPosition(this.context, addAction, 'after-wrapper');
        this.formBuilder.renderElementInPosition(this.context, label, 'after-wrapper');
        this.formBuilder.renderElementInPosition(this.context, error, 'after-wrapper');
    }

    buildField(context) {
        // Start Input Wrapper
        const wrapper = this.formBuilder.createWrapperElement(this.field.inputWrapper || [], context);
        // Before Input
        wrapper.appendChild(this.formBuilder.stringToHTML(this.field.before || ''));
        let elm = '';
        if (this.viewOnly && this.field.view) {
            elm = this.formBuilder.stringToHTML(this.field.view);
        } else if (this.viewOnly && !['checkbox', 'radio'].includes(this.field.type)) {
            elm = document.createElement('div');
            elm.setAttribute('data-view-only', 'true');
            if (this.field.type === 'file') {
                elm.setAttribute('data-multiple', (this.field.multiple ? 'true' : 'false'));
                elm.setAttribute('class', `frm-view-only type-${this.field.type}`);
                const ulElm = document.createElement('ul');
                ulElm.setAttribute('class', 'file-list list-unstyled');
                for (let file of this.field.files) {
                    const liElm = document.createElement('li');
                    liElm.setAttribute('class', 'file-item');
                    const linkElm = document.createElement('a');
                    linkElm.setAttribute('href', file);
                    linkElm.setAttribute('target', '_blank');
                    const fileName = file.split('/').pop();
                    linkElm.append(fileName);
                    liElm.append(linkElm);
                    ulElm.appendChild(liElm);
                }
                elm.append(ulElm);
            } else {
                elm.setAttribute('class', `form-control frm-view-only type-${this.field.type}`);
                elm.append(this.field.value);
            }
        } else {
            // Create Input Element
            elm = document.createElement(this.field.type === 'textarea' ? 'textarea' : 'input');
            // Set Attributes
            this.setElementAttributes(elm);
            if (this.viewOnly && ['checkbox', 'radio'].includes(this.field.type)) {
                elm.setAttribute('readonly', 'readonly');
                elm.setAttribute('disabled', 'disabled');
                elm.setAttribute('data-view-only', 'true');
            }
        }
        // Append Input to Context
        wrapper.appendChild(elm);
        // After Input
        wrapper.appendChild(this.formBuilder.stringToHTML(this.field.after || ''));
    }

    setElementAttributes(elm) {
        if (this.field.type !== 'textarea') {
            elm.setAttribute('type', this.field.type);
        }

        for (let attr in this.field.attributes) {
            const value = this.field.attributes[attr];
            elm.setAttribute(!isNaN(attr) ? value : attr, !isNaN(attr) ? true : value);
        }

        if (this.field.rules) {
            elm.setAttribute('data-rules', JSON.stringify(this.field.rules));
        }

        if (this.field.messages) {
            elm.setAttribute('data-messages', JSON.stringify(this.field.messages));
        }

        if (this.field.value !== null) {
            if (this.field.type === 'textarea') {
                elm.innerHTML = this.field.value;
            } else if (this.field.type !== 'file') {
                elm.setAttribute('value', this.field.value);
            }
        }
    }

    render() {
        return this.context;
    }

}

export class ZkFileElement extends ZkElement {

    constructor(field, formBuilder = null) {
        super(field, formBuilder);
    }

}

export class ZkRadioElement extends ZkElement {

    constructor(field, formBuilder = null) {
        super(field, formBuilder);
    }

}

export class ZkCheckboxElement extends ZkElement {

    constructor(field, formBuilder = null) {
        super(field, formBuilder);
    }

}

export class ZkSelectElement extends ZkElement {

    constructor(field, formBuilder = null) {
        super(field, formBuilder);
    }

    buildField(context) {
        // Start Input Wrapper
        const wrapper = this.formBuilder.createWrapperElement(this.field.inputWrapper || [], context);
        // Before Input
        wrapper.appendChild(this.formBuilder.stringToHTML(this.field.before || ''));

        if (this.viewOnly) {
            this.createViewOnlyElement(wrapper);
        } else {
            this.createSelectElement(wrapper);
        }

        // After Input
        wrapper.appendChild(this.formBuilder.stringToHTML(this.field.after || ''));
    }

    createViewOnlyElement(context) {
        if (this.field.view) {
            const elm = this.formBuilder.stringToHTML(this.field.view);
            context.appendChild(elm);
        } else {
            const elm = document.createElement('div');
            elm.setAttribute('class', `frm-view-only type-${this.field.type}`);
            elm.setAttribute('data-view-only', 'true');
            elm.setAttribute('data-multiselect', (this.isMultiselect() ? 'true' : 'false'));
            const ulElm = document.createElement('ul');
            ulElm.setAttribute('class', 'list-unstyled');
            // Options
            if (this.field.options) {
                for (let option of this.field.options) {
                    if (option.optgroup) {
                        for (let opt of option.options) {
                            if (opt.selected) {
                                const optElm = document.createElement('li');
                                optElm.append(opt.label);
                                ulElm.appendChild(optElm);
                            }
                        }
                    } else if (option.selected) {
                        const optElm = document.createElement('li');
                        optElm.append(option.label);
                        ulElm.appendChild(optElm);
                    }
                }
            }
            elm.append(ulElm);
            context.appendChild(elm);
        }
    }

    createSelectElement(context) {
        // Create Select Element
        const elm = document.createElement('select');
        // Set Attributes
        for (let attr in this.field.attributes) {
            const value = this.field.attributes[attr];
            elm.setAttribute(!isNaN(attr) ? value : attr, !isNaN(attr) ? true : value);
        }
        // Rules
        if (this.field.rules) {
            elm.setAttribute('data-rules', JSON.stringify(this.field.rules));
        }
        // Messages
        if (this.field.messages) {
            elm.setAttribute('data-messages', JSON.stringify(this.field.messages));
        }
        // Options
        if (this.field.options) {
            for (let option of this.field.options) {
                if (option.optgroup) {
                    const optgroupElm = this.createOptGroupElement(option);
                    elm.appendChild(optgroupElm);
                } else {
                    const optElm = this.createOptionElement(option);
                    elm.appendChild(optElm);
                }
            }
        }
        // Append Input to Context
        context.appendChild(elm);
    }

    isMultiselect() {
        return this.field.multiselect;
    }

    optionAttributes(attributes, exludes = []) {
        let attrs = {};
        for (let attr in attributes) {
            if (exludes.includes(attr)) continue;
            attrs[attr] = attributes[attr];
        }

        return attrs;
    }

    isSelected(value) {
        return (
            (value !== null && this.field.value !== null) && (
                (this.isMultiselect() && this.field.value.includes(value)) ||
                (!this.isMultiselect() && this.field.value == value)
            )
        )
    }

    createOptGroupElement(optgroup) {
        const optgroupElm = document.createElement('optgroup');
        optgroupElm.label = optgroup.label;
        const optAttrs = this.optionAttributes(optgroup, ['label', 'optgroup', 'options']);
        for (let attr in optAttrs) {
            const value = optAttrs[attr];
            optgroupElm.setAttribute(!isNaN(attr) ? value : attr, !isNaN(attr) ? true : value);
        }
        for (let opt of optgroup.options) {
            const optElm = this.createOptionElement(opt);
            optgroupElm.appendChild(optElm);
        }
        return optgroupElm;
    }

    createOptionElement(option) {
        const optElm = document.createElement('option');
        optElm.value = option.value;
        optElm.text = option.label;
        // Attributes
        const attrs = this.optionAttributes(option, ['label', 'value']);
        for (let attr in attrs) {
            const value = attrs[attr];
            optElm.setAttribute(!isNaN(attr) ? value : attr, !isNaN(attr) ? true : value);
        }
        return optElm;
    }

}

export class ZkItemgroupElement extends ZkElement {

    constructor(field, formBuilder = null) {
        super(field, formBuilder);
    }

    buildField(context) {
        // Before Itemgroup
        context.appendChild(this.formBuilder.stringToHTML(this.field.before || ''));

        // Start Item Wrapper
        const wrapper = this.formBuilder.createWrapperElement(this.field.itemWrapper || [], context);

        // Group Elements
        for (let item of this.field.items) {
            const element = this.formBuilder.createElementInstance(item);
            const content = element.render();
            if (content) wrapper.appendChild(content);
        }

        // After Itemgroup
        context.appendChild(this.formBuilder.stringToHTML(this.field.after || ''));
    }

}

export class ZkGroupElement extends ZkElement {

    constructor(field, formBuilder = null) {
        super(field, formBuilder);
    }

    buildField(context) {
        // Before Group
        context.appendChild(this.formBuilder.stringToHTML(this.field.before || ''));

        // Start Field Wrapper
        const wrapper = this.formBuilder.createWrapperElement(this.field.fieldWrapper || [], context);

        // Group Elements
        for (let field of this.field.fields) {
            const element = this.formBuilder.createElementInstance(field);
            const content = element.render();
            if (content) wrapper.appendChild(content);
        }

        // After Group
        context.appendChild(this.formBuilder.stringToHTML(this.field.after || ''));
    }

}

export class ZkMultipleElement extends ZkElement {

    constructor(field, formBuilder = null) {
        super(field, formBuilder);
    }

    buildField(context) {
        // Before Multiple
        context.appendChild(this.formBuilder.stringToHTML(this.field.before || ''));

        // Content Wrapper
        const contentWrapper = this.formBuilder.createWrapperElement(this.field.contentWrapper || [], context);

        // Multiple Rows
        for (let row of (this.field.rows || [])) {
            this.buildRow(contentWrapper, row);
        }

        // After Multiple
        context.appendChild(this.formBuilder.stringToHTML(this.field.after || ''));
    }

    buildRow(context, row) {
        const removeAction = !this.viewOnly ? this.formBuilder.createElementInstance(row.removeAction) : null;

        // Start Row Wrapper
        const wrapper = this.formBuilder.createWrapperElement(row.wrapper.wrapper || [], context);

        // Action - before
        this.formBuilder.renderElementInPosition(wrapper, removeAction, 'before');

        // Start Field Wrapper
        const fieldWrapper = row.wrapper.fieldWrapper || [];
        const fwrapper = this.formBuilder.createWrapperElement(fieldWrapper.wrapper || [], wrapper);

        // Row Elements
        for (let field of row.fields) {
            // Create Element
            const element = this.formBuilder.createElementInstance(field);
            const content = element.render();
            if (content) fwrapper.appendChild(content);
        }

        // Action - after
        this.formBuilder.renderElementInPosition(wrapper, removeAction, 'after');

        return wrapper;
    }

}

export class ZkMultipleActionElement {

    constructor(action, formBuilder = null, parent = null) {
        this.action = action || null;
        this.formBuilder = formBuilder;
        this.parent = parent;
        this.context = document.createDocumentFragment();
        this.init();
    }

    init() {
        if (!this.action || !this.action.show) return;

        // Start Action Wrapper
        const wrapper = this.formBuilder.createWrapperElement(this.action.wrapper || [], this.context);

        // Create Action
        const elm = document.createElement(this.action.tag);
        // Set Attributes
        for (let attr in this.action.attributes) {
            const value = this.action.attributes[attr];
            elm.setAttribute(!isNaN(attr) ? value : attr, !isNaN(attr) ? true : value);
        }
        if (this.action.rowObject) {
            elm.setAttribute('data-row-object', JSON.stringify(this.action.rowObject));
        }
        // Action Text
        elm.appendChild(this.formBuilder.stringToHTML(this.action.label || ''));

        // Append Action to Context
        wrapper.appendChild(elm);
    }

    position() {
        return this.action?.position || 'before';
    }

    render(position = 'before') {
        return (this.action && this.position() === position) ? this.context : '';
    }
}

export class ZkTabElement extends ZkElement {

    constructor(field, formBuilder = null) {
        super(field, formBuilder);
    }

    buildField(context) {
        // Before Tab
        context.appendChild(this.formBuilder.stringToHTML(this.field.before || ''));

        // Tab Wrapper
        const tabWrapper = this.formBuilder.createWrapperElement(this.field.tabWrapper || [], context);
        // Tab Nav
        for (let tab of this.field.tabs) {
            // Item Wrapper
            const itemWrapper = this.formBuilder.createWrapperElement(tab.itemWrapper.wrapper || [], tabWrapper);
            // Nav Item
            itemWrapper.appendChild(this.formBuilder.stringToHTML(tab.label));
        }

        // Tab Content
        const contentWrapper = this.formBuilder.createWrapperElement(this.field.contentWrapper || [], context);
        // Tab Panel
        for (let tab of this.field.tabs) {
            // Start Panel Wrapper
            const panelWrapper = this.formBuilder.createWrapperElement(tab.panelWrapper.wrapper || [], contentWrapper);
            // Panel Elements
            for (let field of tab.fields) {
                const element = this.formBuilder.createElementInstance(field);
                const content = element.render();
                if (content) panelWrapper.appendChild(content);
            }
        }
        // After Tab
        context.appendChild(this.formBuilder.stringToHTML(this.field.after || ''));
    }

}

export class ZkButtons {

    constructor(buttons, formBuilder = null) {
        this.buttons = buttons || null;
        this.formBuilder = formBuilder;
        this.context = document.createDocumentFragment();
        this.init();
    }

    init() {
        if (!this.buttons) return;

        // Buttons Wrapper
        const wrapper = this.formBuilder.createWrapperElement(this.buttons.wrapper || [], this.context);

        // Create Actions
        this.buildActions(wrapper);

    }

    buildActions(context) {
        for (let action of this.buttons.actions) {
            const element = this.formBuilder.createButtonInstance(action);
            const content = element.render();
            if (content) context.appendChild(content);
        }
    }

    position() {
        return (this.buttons?.position) ? this.buttons.position : 'form-bottom';
    }

    render(position = 'form-bottom') {
        return (this.buttons && this.position() === position) ? this.context : '';
    }

}

export class ZkAction {

    constructor(action, formBuilder = null) {
        this.action = action;
        this.formBuilder = formBuilder;
        this.context = document.createDocumentFragment();
        this.init();
    }

    init() {
        if (!this.action) return;

        // Action Wrapper
        const wrapper = this.formBuilder.createWrapperElement(this.action.wrapper || [], this.context);
        // Create Action
        const elm = document.createElement(this.action.tag);
        // Set Attributes
        for (let attr in this.action.attributes) {
            const value = this.action.attributes[attr];
            elm.setAttribute(!isNaN(attr) ? value : attr, !isNaN(attr) ? true : value);
        }
        // Action Before Text
        elm.appendChild(this.formBuilder.stringToHTML(this.action.before || ''));
        // Action Text
        elm.appendChild(this.formBuilder.stringToHTML(this.action.text));
        // Action After Text
        elm.appendChild(this.formBuilder.stringToHTML(this.action.after || ''));
        // Append Action to Context
        wrapper.appendChild(elm);
    }

    render() {
        return this.context;
    }
}

export default class ZkFormBuilder {

    static ZkErrorElement = ZkErrorElement;
    static ZkLabelElement = ZkLabelElement;
    static ZkElement = ZkElement;
    static ZkFileElement = ZkFileElement;
    static ZkRadioElement = ZkRadioElement;
    static ZkCheckboxElement = ZkCheckboxElement;
    static ZkSelectElement = ZkSelectElement;
    static ZkItemgroupElement = ZkItemgroupElement;
    static ZkGroupElement = ZkGroupElement;
    static ZkMultipleElement = ZkMultipleElement;
    static ZkMultipleActionElement = ZkMultipleActionElement;
    static ZkTabElement = ZkTabElement;
    static ZkButtons = ZkButtons;
    static ZkAction = ZkAction;

    form = null;
    validator = null;
    frmObj = null;
    frmContainer = null;
    context = null;
    options = {
        multiple: {
            addButton: '[data-action="add-row"]',
            removeButton: '[data-action="remove-row"]',
        },
        loading: '<div class="text-center m-4">Loading...</div>',
    };
    subscribers = {};
    subscribersData = {};

    constructor(options = {}) {
        this.setOptions(options);
        this.setValidatorObj(
            typeof ZkFormValidator !== "undefined" && ZkFormValidator ||
            (typeof window !== "undefined" && window?.ZkFormValidator) ||
            (typeof window !== "undefined" && window?.ZkValidator?.ZkFormValidator) ||
            null
        );
        this.context = typeof document !== "undefined" && document.createDocumentFragment();
    }

    /**
     * Register or override a class dynamically (both instance and class level supported)
     */
    static registerElement(name, elementObj) {
        // Check if the element is a constructor
        if (typeof elementObj !== "function") {
            console.error(`Invalid element class for "${name}". Must be a constructor.`);
            return;
        }
        // Register as a static property of the class
        this[name] = elementObj;
    }

    setOptions(options) {
        this.options = { ...this.options, ...options };
    }

    getOptions() {
        return this.options;
    }

    getForm() {
        return this.form;
    }

    setForm(form) {
        form = (typeof form === 'string') ? document.getElementById(form) : form;
        if (form == null || (form && form.tagName !== 'FORM')) {
            console.error('Form not found');
            return;
        }
        this.form = form;
        this.initMultiple();

        // Init Validator
        if (!this.validator && this.validatorObj) {
            this.validator = new this.validatorObj(this.form);
        }
    }

    setValidatorObj(validatorObj) {
        this.validatorObj = validatorObj;
    }

    getValidatorObj() {
        return this.validatorObj;
    }

    setValidator(validator) {
        this.validator = validator;
    }

    getValidator() {
        return this.validator;
    }

    validate() {
        if (this.validator) {
            return this.validator.validate();
        }
        return true;
    }

    setFormObject(frmObj) {
        this.frmObj = (typeof frmObj === 'string') ? JSON.parse(frmObj) : frmObj;
    }

    render(container, frmObj) {
        this.frmContainer = document.getElementById(container) || document.querySelector(container);
        if (!this.frmContainer) {
            this.dispatch('beforeRender', { status: 'error', message: 'Container not found' });
            console.error('Container not found');
            return;
        }
        this.generate(frmObj);
    }

    generate(frmObj) {

        if (frmObj) this.setFormObject(frmObj);

        this.dispatch('beforeRender', { status: 'success', container: this.frmContainer, frmObj: this.frmObj });

        // Clear Container
        this.frmContainer.innerHTML = '';

        // Container Loading
        this.frmContainer.appendChild(this.stringToHTML(this.options.loading));

        this.buildForm();

        // Append to Container
        this.frmContainer.innerHTML = '';
        this.frmContainer.appendChild(this.context);

        // Init Validator
        if (this.validatorObj) this.validator = new this.validatorObj(this.form);

        // Init Multiple
        this.initMultiple();

        this.dispatch('afterRender', { status: 'success', container: this.frmContainer });

        // Free up memory
        this.frmObj = null;

        // top scroll
        // if (this.frmContainer.scrollIntoView) {
        //     this.frmContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        // }
    }

    buildForm() {
        if (!this.frmObj) {
            this.dispatch('beforeBuildForm', { status: 'error', message: 'Form object not found' });
            console.error('Form object not found');
            return;
        }
        this.context = document.createDocumentFragment();
        // Start Form Wrapper
        const wrapper = this.createWrapperElement(this.frmObj?.wrapper || [], this.context);
        // Before Render Form
        wrapper.appendChild(this.stringToHTML(this.frmObj.beforeRender || ''));
        // Create Form
        this.form = document.createElement('form');
        this.dispatch('beforeBuildForm', { status: 'success', form: this.form });
        // Before Form
        this.form.appendChild(this.stringToHTML(this.frmObj.before || ''));
        // Set Attributes
        if (this.frmObj.action) this.form.setAttribute('action', this.frmObj.action);

        this.form.setAttribute('method', (this.frmObj.method === 'GET' ? 'GET' : 'POST'));
        for (let attr in this.frmObj.attributes) {
            const value = this.frmObj.attributes[attr];
            this.form.setAttribute(isNaN(attr) ? attr : value, isNaN(attr) ? value : true);
        }
        // Add Form Hidden Fields
        this.addFormHiddenFields();
        // Form Buttons
        const buttons = this.createElementInstance(this.frmObj.buttons, this);
        // Form Buttons - Top
        this.renderElementInPosition(this.form, buttons, 'form-top');
        // Build Form Elements
        this.buildField(this.frmObj.fields);
        // Form Buttons - Bottom
        this.renderElementInPosition(this.form, buttons, 'form-bottom');
        // After Form
        this.form.appendChild(this.stringToHTML(this.frmObj.after || ''));
        // Append Form to Context
        wrapper.appendChild(this.form);
        // After Form
        wrapper.appendChild(this.stringToHTML(this.frmObj.afterRender || ''));

        this.dispatch('afterBuildForm', { status: 'success', form: this.form });
    }

    buildField(fields) {
        // Check is Array
        if (Array.isArray(fields)) {
            for (let field of fields) {
                this.buildField(field);
            }
        } else {
            const element = this.createElementInstance(fields);
            this.renderElement(this.form, element);
        }
    }

    createElementInstance(fields, ...args) {
        if (!fields || !fields?.jsElement) return null;
        const element = this[fields.jsElement] || ZkFormBuilder[fields.jsElement] || null;
        if (!element) {
            console.error(`${fields.jsElement} class not found`);
            return null;
        }
        return new element(fields, this, ...args);
    }

    createButtonInstance(buttons) {
        if (!buttons || !buttons?.jsElement) return null;
        const buttonElement = this[buttons.jsElement] || ZkFormBuilder[buttons.jsElement];
        if (!buttonElement) {
            console.error(`${buttons.jsElement} class not found`);
            return null;
        }
        return new buttonElement(buttons, this);
    }

    renderElement(context, element) {
        if (element) {
            const content = element.render();
            if (content) context.appendChild(content);
        }
    }

    renderElementInPosition(context, element, position) {
        if (element) {
            const content = element.render(position);
            if (content) context.appendChild(content);
        }
    }

    createWrapperElement(wrapper, context) {
        let currentElement = context;
        for (let wrap of wrapper) {
            // Before Wrapper
            if (wrap.before) currentElement.appendChild(this.stringToHTML(wrap.before));
            const element = document.createElement(wrap.tag);
            this.setAttributesFromString(element, wrap.attributes);
            currentElement.appendChild(element);
            // After Wrapper
            if (wrap.after) currentElement.appendChild(this.stringToHTML(wrap.after));
            currentElement = element;
        }
        return currentElement;
    }

    stringToHTML(html) {
        if (!html) return document.createDocumentFragment();
        const template = document.createElement('template');
        template.innerHTML = ('' + html).trim();
        return template.content;
    }

    setAttributesFromString(element, attrString) {
        if (!attrString) return;
        const regex = /([a-zA-Z0-9-]+)(?:=["']([^"']*)["'])?/g;
        for (const match of attrString.matchAll(regex)) {
            element.setAttribute(match[1], match[2] || true);
        }
    }

    addMoreEvent(addElement, container = null, rowObject = null, jsElement = null) {
        const _container = container || addElement.getAttribute('data-add-prefix');
        let _rowObject = rowObject || addElement.getAttribute('data-row-object');
        if (typeof _rowObject === 'string') _rowObject = JSON.parse(_rowObject);
        const _jsElement = jsElement || (_rowObject?.jsElement || addElement.getAttribute('data-js-element'));

        if (!_jsElement || !_container || !_rowObject) return;

        const context = this.form.querySelector(`[data-prefix="${_container}"]`);
        if (!context) return;

        const listener = this.debounce((event) => {
            event.preventDefault();
            const element = this.createElementInstance({ jsElement: _jsElement });
            if (element) {
                const rowName = _rowObject.name;
                const rowPrefix = _rowObject.rowPrefix || this.toBracketNotation(rowName);
                const uniqueId = this.generateRandomString();
                const timeStr = new Date().getTime();

                // Replace placeholders with unique IDs
                let rowData = JSON.stringify(_rowObject);
                rowData = rowData.replaceAll(rowName, rowName.replaceAll('{{index}}', `{{${uniqueId}}}`));
                rowData = rowData.replaceAll(rowPrefix, rowPrefix.replaceAll('{{index}}', `{{${uniqueId}}}`));
                rowData = rowData.replaceAll(`{{${uniqueId}}}`, timeStr);

                // Create and append the new row context
                const rowContext = element.buildRow(document.createDocumentFragment(), JSON.parse(rowData));
                context.appendChild(rowContext);

                // Add / Remove If nested multiple
                rowContext.querySelectorAll(this.options.multiple.addButton).forEach((_addElement) => {
                    this.addMoreEvent(_addElement);
                });
                rowContext.querySelectorAll(this.options.multiple.removeButton).forEach((_removeElement) => {
                    this.addRemoveEvent(_removeElement);
                });

                // Validate the new row
                if (this.validator && this.validator.addContextFields) {
                    this.validator.addContextFields(rowContext);
                }
                this.dispatch('addRow', { _container: _container, context: rowContext, rowName, rowPrefix, uniqueId, timeStr });
            }
        }, 150);

        addElement.removeEventListener('click', listener);
        addElement.addEventListener('click', listener);
    }

    addRemoveEvent(removeElement, container = null) {
        const _container = container || removeElement.getAttribute('data-remove-prefix');
        if (!_container) return;

        const listener = this.debounce((event) => {
            event.preventDefault();
            const context = this.form.querySelector(`[data-row-prefix="${_container}"]`);
            if (!context) return;
            this.validator.removeContextFields(context);
            this.dispatch('removeRow', { _container: _container, context: context });
            context.remove();
        }, 100);

        removeElement.removeEventListener('click', listener);
        removeElement.addEventListener('click', listener);
    }

    initMultiple() {
        const addElements = this.form.querySelectorAll(this.options.multiple.addButton);
        addElements.forEach((addElement) => {
            this.addMoreEvent(addElement);
        });
        const removeElements = this.form.querySelectorAll(this.options.multiple.removeButton);
        removeElements.forEach((removeElement) => {
            this.addRemoveEvent(removeElement);
        });
    }

    addFormHiddenFields() {
        if (this.frmObj.csrf) this.addHiddenField('_token', this.frmObj.csrf_token);
        if (this.frmObj.frmKey) this.addHiddenField('_form', this.frmObj.frmKey);
        if (!['GET', 'POST'].includes(this.frmObj.method)) {
            this.addHiddenField('_method', this.frmObj.method);
        }
        if (this.frmObj.key) this.addHiddenField('_key', this.frmObj.key);
        if (this.frmObj.metaData) this.addHiddenField('_metaData', this.frmObj.metaData);
    }

    addHiddenField(name, value, attributes = {}) {
        const input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('name', name);
        input.setAttribute('value', value);
        // Set Attributes
        for (let attr in attributes) {
            const value = this.error.attributes[attr];
            input.setAttribute(isNaN(attr) ? attr : value, isNaN(attr) ? value : true);
        }
        this.form.appendChild(input);
    }

    toBracketNotation(str, seprarator = '.') {
        if (!str.includes(seprarator)) return str; // No seprarator, return the original string
        return str.split(seprarator).map((item, index) => {
            return index === 0 ? item : `[${item}]`;
        }).join('');
    }

    generateRandomString(length = 5) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    subscribe(eventName, callback, lastData = false) {
        this.subscribers[eventName] = this.subscribers[eventName] || [];
        this.subscribers[eventName].push(callback);

        // Call the callback with the last data
        // if (lastData && this.subscribersData[eventName]) {
        //     callback(this.subscribersData[eventName]);
        // }
    }

    unsubscribe(eventName, callback) {
        if (!this.subscribers[eventName]) return;
        this.subscribers[eventName] = this.subscribers[eventName].filter(subscriber => subscriber !== callback);
    }

    dispatch(eventName, data) {
        // Set the last data for the event
        // this.subscribersData[eventName] = data;
        if (!this.subscribers[eventName]) return;
        this.subscribers[eventName].forEach(subscriber => subscriber(data));
    }

    debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }
}