export const ZkValidatorUtils = {
  // Check if the value is empty
  isEmpty(value) {
    return value === null || value === undefined || (typeof value === "string" && value.trim() === "");
  },
  // Check if the value is a number
  isNumber(value) {
    return typeof value === "number" || (!isNaN(value) && !isNaN(parseFloat(value)));
  },
  // Get label text for the specified element
  getLabelText(elm) {
    const element = (elm instanceof HTMLElement) ? elm : (document.getElementById(elm) || document.querySelector(`[name="${elm}"]`));
    const label = element?.id ? document.querySelector(`label[for="${element.id}"]`) : null;
    return label ? label.innerText : element?.name || element?.placeholder || (typeof element === "string" ? element : "field");
  },
  // Condition Checking
  checkCondition(matchValue, operator, value) {
    switch (operator) {
      case "=":
      case "==":
        return matchValue == value;
      case "===":
        return matchValue === value;
      case "!=":
        return matchValue != value;
      case ">":
        return matchValue > value;
      case "<":
        return matchValue < value;
      case ">=":
        return matchValue >= value;
      case "<=":
        return matchValue <= value;
      default:
        return false;
    }
  },
  // Get MIME type from file extension
  getMimeType(ext = null) {
    const result = [];

    if (!ext) {
      for (const types of Object.values(this.mimeTypes)) {
        result.push(...types);
      }
      return [...new Set(result)];
    }

    const exts = Array.isArray(ext) ? ext : [ext];
    for (const ext of exts) {
      if (this.mimeTypes[ext]) {
        result.push(...this.mimeTypes[ext]);
      }
    }
    return [...new Set(result)];
  },
  // Get file extension from MIME type
  getExtensionFromMimeType(mime) {
    const result = [];
    const mimes = Array.isArray(mime) ? mime : [mime];
    for (const ext in this.mimeTypes) {
      const types = this.mimeTypes[ext];
      for (let i = 0; i < types.length; i++) {
        if (mimes.includes(types[i])) {
          result.push(ext);
          break;
        }
      }
    }
    return result;
  },
  // MIME types
  mimeTypes: {
    // Documents
    pdf: ['application/pdf', 'application/acrobat', 'application/nappdf', 'application/x-pdf', 'image/pdf'],
    doc: ['application/msword', 'application/vnd.ms-word', 'application/x-msword', 'zz-application/zz-winassoc-doc'],
    docx: ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    xls: ['application/vnd.ms-excel', 'application/msexcel', 'application/x-msexcel', 'zz-application/zz-winassoc-xls'],
    xlsx: ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
    ppt: ['application/vnd.ms-powerpoint', 'application/mspowerpoint', 'application/powerpoint', 'application/x-mspowerpoint'],
    pptx: ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
    txt: ['text/plain'],
    rtf: ['application/rtf', 'text/rtf'],
    odt: ['application/vnd.oasis.opendocument.text'],
    ods: ['application/vnd.oasis.opendocument.spreadsheet'],

    // Images
    jpg: ['image/jpeg', 'image/pjpeg'],
    jpeg: ['image/jpeg', 'image/pjpeg'],
    png: ['image/png', 'image/apng', 'image/vnd.mozilla.apng'],
    gif: ['image/gif'],
    tiff: ['image/tiff'],
    bmp: ['image/bmp', 'image/x-bmp', 'image/x-ms-bmp'],
    webp: ['image/webp'],
    svg: ['image/svg+xml', 'image/svg'],
    ico: ['application/ico', 'image/ico', 'image/icon', 'image/vnd.microsoft.icon', 'image/x-ico', 'image/x-icon', 'text/ico'],

    // Audio
    mp3: ['audio/mpeg', 'audio/mp3', 'audio/x-mp3', 'audio/x-mpeg', 'audio/x-mpg'],
    wav: ['audio/wav', 'audio/vnd.wave', 'audio/wave', 'audio/x-wav'],
    ogg: ['audio/ogg', 'audio/vorbis', 'audio/x-flac+ogg', 'audio/x-ogg', 'audio/x-oggflac', 'audio/x-speex+ogg', 'audio/x-vorbis', 'audio/x-vorbis+ogg', 'video/ogg', 'video/x-ogg', 'video/x-theora', 'video/x-theora+ogg'],
    aac: ['audio/aac', 'audio/x-aac', 'audio/x-hx-aac-adts'],
    flac: ['audio/flac', 'audio/x-flac'],

    // Video
    mp4: ['video/mp4', 'application/mp4', 'video/mp4v-es', 'video/x-m4v'],
    webm: ['video/webm'],
    avi: ['video/avi', 'video/divx', 'video/msvideo', 'video/vnd.avi', 'video/vnd.divx', 'video/x-avi', 'video/x-msvideo'],
    mov: ['video/quicktime'],
    mkv: ['video/x-matroska'],
    flv: ['video/x-flv', 'application/x-flash-video', 'flv-application/octet-stream', 'video/flv'],
    ogv: ['video/ogg', 'video/x-ogg'],

    // Code & Text
    html: ['text/html', 'application/xhtml+xml'],
    json: ['application/json', 'application/schema+json'],
    xml: ['application/xml', 'text/xml'],
    csv: ['text/csv', 'application/csv', 'text/x-comma-separated-values', 'text/x-csv']
  },
  // Dispatch custom events
  dispatchCustomEvent(element, eventName, detail = {}) {
    const event = new CustomEvent(eventName, {
      bubbles: true,
      cancelable: true,
      detail,
    });
    element.dispatchEvent(event);
  },
  // Get the comparison date based on the input date and format
  getComparisonDate(date, format = "YYYY-MM-DD") {
    const now = new Date();
    switch (date.toLowerCase()) {
      case 'today':
        return new Date(now.setHours(0, 0, 0, 0)); // Today at 00:00:00
      case 'tomorrow':
        return new Date(now.setDate(now.getDate() + 1).setHours(0, 0, 0, 0)); // Tomorrow at 00:00:00
      case 'week':
        return new Date(now.setDate(now.getDate() + 7).setHours(0, 0, 0, 0)); // One week from now at 00:00:00
      default:
        // Parse the default date according to the format
        return ZkValidatorUtils.parseDate(date, format)?.date || null; // Return the parsed date or null if parsing fails
    }
  },
  parseDate(dateStr, format) {
    const lowerFormat = format.toLowerCase();
    const regex = {
      'yyyy-mm-dd': /^(\d{4})[-](\d{2})[-](\d{2})$/,
      'yyyy/mm/dd': /^(\d{4})[\/](\d{2})[\/](\d{2})$/,
      'dd-mm-yyyy': /^(\d{2})[-](\d{2})[-](\d{4})$/,
      'dd/mm/yyyy': /^(\d{2})[\/](\d{2})[\/](\d{4})$/,
      'mm-dd-yyyy': /^(\d{2})[-](\d{2})[-](\d{4})$/,
      'mm/dd/yyyy': /^(\d{2})[\/](\d{2})[\/](\d{4})$/
    };

    const formatRegex = regex[lowerFormat];
    if (!formatRegex) return null; // If the format is not supported, return null

    const match = dateStr.match(formatRegex);
    if (!match) return null; // If input doesn't match the regex, return null

    let [_, part1, part2, part3] = match;
    let year, month, day;

    if (lowerFormat === 'yyyy-mm-dd' || lowerFormat === 'yyyy/mm/dd') {
      year = part1;
      month = part2;
      day = part3;
    } else if (lowerFormat === 'dd-mm-yyyy' || lowerFormat === 'dd/mm/yyyy') {
      year = part3;
      month = part2;
      day = part1;
    } else if (lowerFormat === 'mm-dd-yyyy' || lowerFormat === 'mm/dd/yyyy') {
      year = part3;
      month = part1;
      day = part2;
    }

    const date = new Date(year, month - 1, day);
    date.setHours(0, 0, 0, 0); // Set time to midnight
    // Return the constructed date object
    return {
      year: parseInt(year),
      month: parseInt(month),
      day: parseInt(day),
      date: date
    };
  }
};

export const ZkValidatorMessages = {
  // Add your custom validator message, if it exists, it will be overridden
  addMessage: function (name, message) {
    if (typeof name === "string" && typeof message === "string") {
      this[name] = message; // 'this' refers to ZkValidatorMessages
    }
  },
  addMessages: function (messages) {
    if (typeof messages === "object" && messages !== null) {
      for (const [name, message] of Object.entries(messages)) {
        this.addMessage(name, message); // Using Object.entries for cleaner iteration
      }
    }
  },
  // Default messages
  nullable: "This field must be null.",
  required: "This field is required.",
  required_if: "This field is required when :other is :value.",
  required_with: "This field is required when :values is present.",
  required_with_all: "This field is required when :values are present.",
  required_without: "This field is required when :values is not present.",
  required_without_all: "This field is required when none of :values are present.",
  email: "This field must be a valid email address.",
  numeric: "This field must be a number.",
  integer: "This field must be an integer.",
  digits: "This field must be :digits digits.",
  digits_between: "This field must be between :min and :max digits.",
  alpha: "This field field must only contain letters.",
  alpha_num: "This field must only contain letters and numbers.",
  alpha_dash: "This field must only contain letters, numbers, dashes, and underscores.",
  url: "This field must be a valid URL.",
  date: "This field must be a valid date.",
  date_between: "This field must be between :min and :max.",
  date_before: "This field must be a date before :date.",
  before_or_equal: "This field must be a date before or equal to :date.",
  date_after: "This field must be a date after :date.",
  after_or_equal: "This field must be a date after or equal to :date.",
  date_format: "This field must match the format :format.",
  json: "This field must be a valid JSON string.",
  ip: "This field must be a valid :version address.",
  contains: "This field is missing a required value.",
  boolean: "This field must be true or false.",
  minlength: "This field must be at least :length characters.",
  maxlength: "This field may not be greater than :length characters.",
  starts_with: "This field must start with one of the following: :prefixes.",
  ends_with: "This field must end with one of the following: :suffixes.",
  in: "This field must be one of the following: :values.",
  not_in: "This field must not be one of the following: :values.",
  same: "This field must match :other.",
  pattern: "This field format is invalid.",
  regex: "This field format is invalid.",
  not_regex: "This field format is invalid.",
  min: "This field must be greater than or equal to :min.",
  max: "This field must be less than or equal to :max.",
  between: "This field must be between :min and :max.",
  size: "This field must be :size kilobytes.",
  min_size: "This field must be greater than or equal to :size kilobytes.",
  max_size: "This field must be less than or equal to :size kilobytes.",
  between_size: "This field must be between :min and :max kilobytes.",
  mimes: "This field must be a file of type: :values.",
  uuid: "This field must be a valid UUID.",
  server: "This field must be valid.",
};

export const ZkValidatorRules = {
  // Add your custom validator rule, if it exists, it will be overridden
  addRule(name, handler, message) {
    if (typeof name === "string" && typeof handler === "function") {
      this[name] = { handler, message };
    }
  },
  addRules(rules) {
    if (typeof rules === "object" && rules !== null) {
      for (const [name, rule] of Object.entries(rules)) {
        this.addRule(name, rule.handler, rule.message);
      }
    }
  },
  // Default rules
  nullable: {
    handler: function (element) {
      return ZkValidatorUtils.isEmpty(element.value);
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.nullable)
        .replace(/:field|:attribute/g, label);
    }
  },
  required: {
    handler: function (element) {
      const type = element.type;
      const tag = element.tagName;

      // Handle checkbox/radio group
      if (["checkbox", "radio"].includes(type)) {
        const group = document.getElementsByName(element.name);
        return Array.from(group).some((el) => el.checked);
      }

      // Handle file input
      if (type === "file") {
        return element.files.length > 0;
      }

      // Handle select (single or multiple)
      if (tag === "SELECT") {
        return Array.from(element.selectedOptions || []).some(opt => opt.value.trim() !== "");
      }

      // Default input/textarea case
      return element.value?.trim() !== "";
    },
    message(element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.required)
        .replace(/:field|:attribute/g, label);
    },
  },
  required_if: {
    handler: function (element, field, operator, value = "") {
      const validOps = ["=", "==", "===", "!=", ">", "<", ">=", "<="];

      // Support optional operator (default '=' if skipped)
      if (!validOps.includes(operator)) {
        value = operator;
        operator = "=";
      }

      const target = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
      if (!target) return false;

      let targetValue;

      switch (target.type) {
        case "checkbox":
        case "radio":
          const selected = Array.from(document.getElementsByName(target.name)).filter(i => i.checked);
          targetValue = selected.map(i => i.value);
          break;
        case "file":
          targetValue = target.files.length;
          break;
        default:
          if (target.tagName === "SELECT") {
            targetValue = Array.from(target.selectedOptions).map(o => o.value.trim());
          } else {
            targetValue = target.value.trim();
          }
      }

      const conditionMet = Array.isArray(targetValue)
        ? targetValue.some(val => ZkValidatorUtils.checkCondition(val, operator, value))
        : ZkValidatorUtils.checkCondition(targetValue, operator, value);

      return conditionMet ? ZkValidatorRules.required.handler(element) : true;
    },
    message: function (element, message = "", field, value) {
      const label = ZkValidatorUtils.getLabelText(element);
      const other = ZkValidatorUtils.getLabelText(field);
      return (message || ZkValidatorMessages.required_if)
        .replace(/:field|:attribute/g, label)
        .replace(":other", other)
        .replace(":value", value);
    },
  },
  required_with: {
    handler: function (element, ...fields) {
      // Only required if at least one of the given fields is present and not empty.
      const anyFieldFilled = fields.some(field => {
        const target = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
        if (!target) return false;
        if (target.type === "checkbox" || target.type === "radio") {
          return Array.from(document.getElementsByName(target.name)).some(i => i.checked);
        }
        if (target.tagName === "SELECT") {
          return Array.from(target.selectedOptions || []).some(opt => opt.value.trim() !== "");
        }
        return target.value?.trim() !== "";
      });
      // If any field is filled, check if the current element is filled
      return anyFieldFilled ? ZkValidatorRules.required.handler(element) : true;
    },
    message: function (element, message = "", ...fields) {
      const label = ZkValidatorUtils.getLabelText(element);
      const other = fields.map(field => ZkValidatorUtils.getLabelText(field)).join(", ");
      return (message || ZkValidatorMessages.required_with)
        .replace(/:field|:attribute/g, label)
        .replace(":values", other);
    },
  },
  required_with_all: {
    handler: function (element, ...fields) {
      // Only required if all of the given fields are present and not empty.
      const allFieldsFilled = fields.every(field => {
        const target = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
        if (!target) return false;
        if (target.type === "checkbox" || target.type === "radio") {
          return Array.from(document.getElementsByName(target.name)).some(i => i.checked);
        }
        if (target.tagName === "SELECT") {
          return Array.from(target.selectedOptions || []).some(opt => opt.value.trim() !== "");
        }
        return target.value?.trim() !== "";
      });
      // If all fields are filled, check if the current element is filled
      return allFieldsFilled ? ZkValidatorRules.required.handler(element) : true;
    },
    message: function (element, message = "", ...fields) {
      const label = ZkValidatorUtils.getLabelText(element);
      const other = fields.map(field => ZkValidatorUtils.getLabelText(field)).join(", ");
      return (message || ZkValidatorMessages.required_with_all)
        .replace(/:field|:attribute/g, label)
        .replace(":values", other);
    },
  },
  required_without: {
    handler: function (element, ...fields) {
      // Only required if at least one of the given fields is missing or empty.
      const anyFieldFilled = fields.some(field => {
        const target = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
        if (!target) return false;
        if (target.type === "checkbox" || target.type === "radio") {
          return Array.from(document.getElementsByName(target.name)).some(i => i.checked);
        }
        if (target.tagName === "SELECT") {
          return Array.from(target.selectedOptions || []).some(opt => opt.value.trim() !== "");
        }
        return target.value?.trim() !== "";
      });
      // If any field is filled, check if the current element is filled
      return !anyFieldFilled ? ZkValidatorRules.required.handler(element) : true;
    },
    message: function (element, message = "", ...fields) {
      const label = ZkValidatorUtils.getLabelText(element);
      const other = fields.map(field => ZkValidatorUtils.getLabelText(field)).join(", ");
      return (message || ZkValidatorMessages.required_without)
        .replace(/:field|:attribute/g, label)
        .replace(":values", other);
    },
  },
  required_without_all: {
    handler: function (element, ...fields) {
      // Only required if all of the given fields are missing or empty.
      const allFieldsFilled = fields.every(field => {
        const target = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
        if (!target) return false;
        if (target.type === "checkbox" || target.type === "radio") {
          return Array.from(document.getElementsByName(target.name)).some(i => i.checked);
        }
        if (target.tagName === "SELECT") {
          return Array.from(target.selectedOptions || []).some(opt => opt.value.trim() !== "");
        }
        return target.value?.trim() !== "";
      });
      // If all fields are filled, check if the current element is filled
      return !allFieldsFilled ? ZkValidatorRules.required.handler(element) : true;
    },
    message: function (element, message = "", ...fields) {
      const label = ZkValidatorUtils.getLabelText(element);
      const other = fields.map(field => ZkValidatorUtils.getLabelText(field)).join(", ");
      return (message || ZkValidatorMessages.required_without_all)
        .replace(/:field|:attribute/g, label)
        .replace(":values", other);
    },
  },
  email: {
    handler: function (element) {
      // Allow empty input or Standard email regex: local@domain.tld
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        /^[\w.-]+@([\w-]+\.)+[a-zA-Z]{2,}$/.test(element.value)
      );
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.email)
        .replace(/:field|:attribute/g, label);
    },
  },
  numeric: {
    handler: function (element) {
      // Allow empty input or Standard numeric regex: -123.45 or 123.45
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        /^-?\d+(\.\d+)?$/.test(element.value)
      );
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.numeric)
        .replace(/:field|:attribute/g, label);
    },
  },
  integer: {
    handler: function (element) {
      // Allow empty input or Standard integer regex: -123 or 123
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        /^-?\d+$/.test(element.value)
      );
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.integer)
        .replace(/:field|:attribute/g, label);
    },
  },
  digits: {
    handler: function (element, digits) {
      // Allow empty input or check if the value is exactly the specified number of digits
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        (new RegExp(`^\\d{${digits}}$`)).test(element.value)
      );
    },
    message: function (element, message = "", digits) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.digits)
        .replace(/:field|:attribute/g, label)
        .replace(":digits", digits);
    },
  },
  digits_between: {
    handler: function (element, min, max) {
      // Allow empty input or check if the value is between the specified range of digits
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        (new RegExp(`^\\d{${min},${max}}$`)).test(element.value)
      );
    },
    message: function (element, message = "", min, max) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.digits_between)
        .replace(/:field|:attribute/g, label)
        .replace(":min", min)
        .replace(":max", max);
    },
  },
  alpha: {
    handler: function (element) {
      // Allow empty input or Standard alpha regex: a-zA-Z
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        /^[a-zA-Z]+$/.test(element.value)
      );
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.alpha)
        .replace(/:field|:attribute/g, label);
    },
  },
  alpha_num: {
    handler: function (element) {
      // Allow empty input or Standard alpha numeric regex: a-zA-Z0-9
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        /^[a-zA-Z0-9]+$/.test(element.value)
      );
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.alpha_num)
        .replace(/:field|:attribute/g, label);
    },
  },
  alpha_dash: {
    handler: function (element) {
      // Allow empty input or Standard alpha dash regex: a-zA-Z0-9_-
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        /^[a-zA-Z0-9_-]+$/.test(element.value)
      );
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.alpha_dash)
        .replace(/:field|:attribute/g, label);
    },
  },
  url: {
    handler: function (element) {
      // Allow empty input or Standard URL regex: http(s)://www.example.com
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      try {
        new URL(element.value);
        return true;
      } catch {
        return false;
      }
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.url)
        .replace(/:field|:attribute/g, label);
    },
  },
  date: {
    handler: function (element, format = "YYYY-MM-DD") {
      // Allow empty input
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      // Check format is true or not
      format = format === "true" ? "YYYY-MM-DD" : format;

      const parseDate = ZkValidatorUtils.parseDate(element.value, format);
      if (!parseDate || isNaN(parseDate.date.getTime())) return false;

      // Check if the date is valid
      const { year, month, day, date } = parseDate;

      const isValidDate = (
        date.getFullYear() === year &&
        date.getMonth() + 1 === month &&
        date.getDate() === day
      );
      // Check if the date is valid
      return isValidDate;
    },
    message: function (element, message = "", format = "YYYY-MM-DD") {
      const label = ZkValidatorUtils.getLabelText(element);
      format = format === "true" ? "YYYY-MM-DD" : format;
      return (message || ZkValidatorMessages.date)
        .replace(/:field|:attribute/g, label)
        .replace(":format", format);
    },
  },
  date_between: {
    handler: function (element, minDate, maxDate, format = "YYYY-MM-DD") {
      // Allow empty input
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      // Parse input date
      const parsed = ZkValidatorUtils.parseDate(element.value, format);
      if (!parsed || isNaN(parsed.date.getTime())) return false;

      const inputTime = parsed.date.getTime();

      // Parse min and max
      const minParsed = ZkValidatorUtils.parseDate(minDate);
      const maxParsed = ZkValidatorUtils.parseDate(maxDate);
      if (!minParsed || !maxParsed) return false;

      const minTime = minParsed.date.getTime();
      const maxTime = maxParsed.date.getTime();

      return inputTime >= minTime && inputTime <= maxTime;
    },
    message: function (element, message = "", minDate, maxDate, format = "YYYY-MM-DD") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.date_between)
        .replace(/:field|:attribute/g, label)
        .replace(":min", minDate)
        .replace(":max", maxDate)
        .replace(":format", format);
    },
  },
  date_before: {
    handler: function (element, date, format = "YYYY-MM-DD") {
      // Allow empty input
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      const inputDate = ZkValidatorUtils.parseDate(element.value, format);
      if (!inputDate || isNaN(inputDate.date.getTime())) return false;

      const compareDate = ZkValidatorUtils.getComparisonDate(date);
      if (!compareDate || isNaN(compareDate.getTime())) return false;

      // Check if inputDate is strictly before compareDate
      return inputDate.date.getTime() < compareDate.getTime();
    },
    message: function (element, message = "", date, format = "YYYY-MM-DD") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.date_before)
        .replace(/:field|:attribute/g, label)
        .replace(":date", date)
        .replace(":format", format);
    },
  },
  before_or_equal: {
    handler: function (element, date, format = "YYYY-MM-DD") {
      // Allow empty input
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      const inputDate = ZkValidatorUtils.parseDate(element.value, format);
      if (!inputDate || isNaN(inputDate.date.getTime())) return false;

      const compareDate = ZkValidatorUtils.getComparisonDate(date);
      if (!compareDate || isNaN(compareDate.getTime())) return false;

      // Check if inputDate is before or equal to compareDate
      return inputDate.date.getTime() <= compareDate.getTime();
    },
    message: function (element, message = "", date, format = "YYYY-MM-DD") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.before_or_equal)
        .replace(/:field|:attribute/g, label)
        .replace(":date", date)
        .replace(":format", format);
    },
  },
  date_after: {
    handler: function (element, date, format = "YYYY-MM-DD") {
      // Allow empty input
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      const inputDate = ZkValidatorUtils.parseDate(element.value, format);
      if (!inputDate || isNaN(inputDate.date.getTime())) return false;

      const compareDate = ZkValidatorUtils.getComparisonDate(date);
      if (!compareDate || isNaN(compareDate.getTime())) return false;

      // Check if inputDate is strictly after compareDate
      return inputDate.date.getTime() > compareDate.getTime();
    },
    message: function (element, message = "", date, format = "YYYY-MM-DD") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.date_after)
        .replace(/:field|:attribute/g, label)
        .replace(":date", date)
        .replace(":format", format);
    },
  },
  after_or_equal: {
    handler: function (element, date, format = "YYYY-MM-DD") {
      // Allow empty input
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      const inputDate = ZkValidatorUtils.parseDate(element.value, format);
      if (!inputDate || isNaN(inputDate.date.getTime())) return false;

      const compareDate = ZkValidatorUtils.getComparisonDate(date);
      if (!compareDate || isNaN(compareDate.getTime())) return false;

      // Check if inputDate is after or equal to compareDate
      return inputDate.date.getTime() >= compareDate.getTime();
    },
    message: function (element, message = "", date, format = "YYYY-MM-DD") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.after_or_equal)
        .replace(/:field|:attribute/g, label)
        .replace(":date", date)
        .replace(":format", format);
    }
  },
  date_format: {
    handler: function (element, format) {
      // Allow empty input
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      const parseDate = ZkValidatorUtils.parseDate(element.value, format);
      if (!parseDate || isNaN(parseDate.date.getTime())) return false;

      const { year, month, day, date } = parseDate;

      const isValidDate = (
        date.getFullYear() === year &&
        date.getMonth() + 1 === month &&
        date.getDate() === day
      );

      return isValidDate;
    },
    message: function (element, message = "", format) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.date_format)
        .replace(/:field|:attribute/g, label)
        .replace(":format", format);
    },
  },
  json: {
    handler: function (element) {
      // Allow empty input or check if the value is valid JSON
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      try {
        const parsed = JSON.parse(element.value.trim());
        return typeof parsed === "object";
      } catch {
        return false;
      }
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.json)
        .replace(/:field|:attribute/g, label);
    },
  },
  ip: {
    handler: function (element, version = "all") {
      // Allow empty input or check if the value is a valid IP address
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      // Check version is true or not
      version = version === "true" ? "all" : version;

      const ipv4 = /^(25[0-5]|2[0-4]\d|1?\d{1,2})(\.(25[0-5]|2[0-4]\d|1?\d{1,2})){3}$/;
      const ipv6 = /^(?:[a-fA-F0-9]{1,4}:){7}[a-fA-F0-9]{1,4}$|^::(?:[a-fA-F0-9]{1,4}:){0,6}[a-fA-F0-9]{1,4}$/;

      if (version === "all") {
        return ipv4.test(element.value) || ipv6.test(element.value);
      }
      if (version === "v4") {
        return ipv4.test(element.value);
      }
      if (version === "v6") {
        return ipv6.test(element.value);
      }
      return false;
    },
    message: function (element, message = "", version = "all") {
      const label = ZkValidatorUtils.getLabelText(element);
      version = version === "true" ? "all" : version;
      if (version === "all") {
        version = "IP";
      } else if (version === "v4") {
        version = "IPv4";
      } else if (version === "v6") {
        version = "IPv6";
      }
      return (message || ZkValidatorMessages.ip)
        .replace(/:field|:attribute/g, label)
        .replace(":version", version);
    },
  },
  contains: {
    handler: function (element, value) {
      // Allow empty input or check if the value contains the specified value
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        element.value.includes(value)
      );
    },
    message: function (element, message = "", value) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.contains)
        .replace(/:field|:attribute/g, label)
        .replace(":contains", value);
    },
  },
  boolean: {
    handler: function (element) {
      // Check if value is empty or is a boolean
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        ["true", "false", "1", "0"].includes(element.value)
      );
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.boolean)
        .replace(/:field|:attribute/g, label);
    },
  },
  minlength: {
    handler: function (element, length) {
      // Allow empty input or check if the value is at least the specified length
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        element.value.length >= length
      );
    },
    message: function (element, message = "", length) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.minlength)
        .replace(/:field|:attribute/g, label)
        .replace(":length", length);
    },
  },
  maxlength: {
    handler: function (element, length) {
      // Allow empty input or check if the value is at most the specified length
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        element.value.length <= length
      );
    },
    message: function (element, message = "", length) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.maxlength)
        .replace(/:field|:attribute/g, label)
        .replace(":length", length);
    },
  },
  starts_with: {
    handler: function (element, ...prefixes) {
      // Allow empty input or check if the value starts with any of the specified prefixes
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        prefixes.some(prefix => element.value.startsWith(prefix))
      );
    },
    message: function (element, message = "", ...prefixes) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.starts_with)
        .replace(/:field|:attribute/g, label)
        .replace(":prefixes", prefixes.join(", "));
    },
  },
  ends_with: {
    handler: function (element, ...suffixes) {
      // Allow empty input or check if the value ends with any of the specified suffixes
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        suffixes.some(suffix => element.value.endsWith(suffix))
      );
    },
    message: function (element, message = "", ...suffixes) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.ends_with)
        .replace(/:field|:attribute/g, label)
        .replace(":suffixes", suffixes.join(", "));
    },
  },
  in: {
    handler: function (element, ...values) {
      // Allow empty input or check if the value is in the specified values
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        values.includes(element.value)
      );
    },
    message: function (element, message = "", ...values) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.in)
        .replace(/:field|:attribute/g, label)
        .replace(":values", values.join(", "));
    },
  },
  not_in: {
    handler: function (element, ...values) {
      // Allow empty input or check if the value is not in the specified values
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        !values.includes(element.value)
      );
    },
    message: function (element, message = "", values) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.not_in)
        .replace(/:field|:attribute/g, label)
        .replace(":values", values.join(", "));
    },
  },
  same: {
    handler: function (element, field) {
      // Check if the value is the same as the specified field
      const sameField = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
      if (sameField == null) {
        return false;
      }
      return element.value === sameField.value;
    },
    message: function (element, message = "", field) {
      const label = ZkValidatorUtils.getLabelText(element);
      const other = ZkValidatorUtils.getLabelText(field);
      return (message || ZkValidatorMessages.same)
        .replace(/:field|:attribute/g, label)
        .replace(":other", other);
    },
  },
  pattern: {
    handler: function (element, pattern) {
      // Allow empty input or check if the value matches the specified pattern
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        (new RegExp(pattern)).test(element.value)
      );
    },
    message: function (element, message = "", pattern) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.pattern)
        .replace(/:field|:attribute/g, label)
        .replace(":pattern", pattern);
    },
  },
  regex: {
    handler: function (element, pattern) {
      // Allow empty input or check if the value matches the specified regex pattern - remove slashes from regex
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        (new RegExp(pattern.replace(/^\/|\/$/g, ""))).test(element.value)
      );
    },
    message: function (element, message = "", pattern) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.regex)
        .replace(/:field|:attribute/g, label)
        .replace(":pattern", pattern);
    },
  },
  not_regex: {
    handler: function (element, pattern) {
      // Allow empty input or check if the value does not match the specified regex pattern - remove slashes from regex
      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        !(new RegExp(pattern.replace(/^\/|\/$/g, ""))).test(element.value)
      );
    },
    message: function (element, message = "", pattern) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.not_regex)
        .replace(/:field|:attribute/g, label)
        .replace(":pattern", pattern);
    },
  },
  min: {
    handler: function (element, min) {
      // Allow empty input or check if the value is at least the specified min value

      // Handle checkbox or radio group
      if (element.type === "checkbox" || element.type === "radio") {
        const inputs = document.getElementsByName(element.name);
        return Array.from(inputs).filter((input) => input.checked).length >= min;
      }

      // Handle select
      if (element.tagName === "SELECT") {
        return Array.from(element.selectedOptions || []).length >= min;
      }

      // Handle file input
      if (element.type === "file") {
        return element.files.length >= min;
      }

      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        parseFloat(element.value) >= min
      );
    },
    message: function (element, message = "", min) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.min)
        .replace(/:field|:attribute/g, label)
        .replace(":min", min);
    },
  },
  max: {
    handler: function (element, max) {
      // Allow empty input or check if the value is at most the specified max value

      // Handle checkbox or radio group
      if (element.type === "checkbox" || element.type === "radio") {
        const inputs = document.getElementsByName(element.name);
        return Array.from(inputs).filter((input) => input.checked).length <= max;
      }

      // Handle select
      if (element.tagName === "SELECT") {
        return Array.from(element.selectedOptions || []).length <= max;
      }

      // Handle file input
      if (element.type === "file") {
        return element.files.length <= max;
      }

      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        parseFloat(element.value) <= max
      );
    },
    message: function (element, message = "", max) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.max)
        .replace(/:field|:attribute/g, label)
        .replace(":max", max);
    },
  },
  between: {
    handler: function (element, min, max) {
      // Allow empty input or check if the value is between the specified min and max values

      // Handle checkbox or radio group
      if (element.type === "checkbox" || element.type === "radio") {
        const inputs = document.getElementsByName(element.name);
        const checked = Array.from(inputs).filter((input) => input.checked).length;
        return checked >= min && checked <= max;
      }
      // Handle select
      if (element.tagName === "SELECT") {
        const selected = Array.from(element.selectedOptions || []).length;
        return selected >= min && selected <= max;
      }

      // Handle file input
      if (element.type === "file") {
        return element.files.length >= min && element.files.length <= max;
      }

      return (
        ZkValidatorUtils.isEmpty(element.value) ||
        (parseFloat(element.value) >= min && parseFloat(element.value) <= max)
      );
    },
    message: function (element, message = "", min, max) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.between)
        .replace(/:field|:attribute/g, label)
        .replace(":min", min)
        .replace(":max", max);
    },
  },
  size: {
    handler: function (element, size) {
      // Allow empty or check if the file size is equal to the specified size
      if (element.files.length === 0) return true;
      const expectedSize = parseFloat(size); // in KB
      for (let i = 0; i < element.files.length; i++) {
        const sizeInKB = parseFloat((element.files[i].size / 1024).toFixed(2));
        if (sizeInKB !== expectedSize) {
          return false;
        }
      }
      return true;
    },
    message: function (element, message = "", size) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.size)
        .replace(/:field|:attribute/g, label)
        .replace(":size", size);
    },
  },
  min_size: {
    handler: function (element, size) {
      // Allow empty or check if the file size is greater than or equal to the specified size
      if (element.files.length === 0) return true;
      const minSize = parseFloat(size); // in KB
      for (let i = 0; i < element.files.length; i++) {
        const sizeInKB = parseFloat((element.files[i].size / 1024).toFixed(2));
        if (sizeInKB < minSize) {
          return false;
        }
      }
      return true;
    },
    message: function (element, message = "", size) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.min_size)
        .replace(/:field|:attribute/g, label)
        .replace(":size", size);
    },
  },
  max_size: {
    handler: function (element, size) {
      // Allow empty or check if the file size is less than or equal to the specified size
      if (element.files.length === 0) return true;
      const maxSize = parseFloat(size); // in KB
      for (let i = 0; i < element.files.length; i++) {
        const sizeInKB = parseFloat((element.files[i].size / 1024).toFixed(2));
        if (sizeInKB > maxSize) {
          return false;
        }
      }
      return true;
    },
    message: function (element, message = "", size) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.max_size)
        .replace(/:field|:attribute/g, label)
        .replace(":size", size);
    },
  },
  between_size: {
    handler: function (element, min, max) {
      // Allow empty or check if the file size is between the specified min and max values
      if (element.files.length === 0) return true;
      const minSize = parseFloat(min); // in KB
      const maxSize = parseFloat(max); // in KB
      for (let i = 0; i < element.files.length; i++) {
        const sizeInKB = parseFloat((element.files[i].size / 1024).toFixed(2));
        if (sizeInKB < minSize || sizeInKB > maxSize) {
          return false;
        }
      }
      return true;
    },
    message: function (element, message = "", min, max) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.between_size)
        .replace(/:field|:attribute/g, label)
        .replace(":min", min)
        .replace(":max", max);
    },
  },
  mimes: {
    handler: function (element, ...allowedExts) {
      // Allow empty or check if the file type is in the allowed extensions
      if (element.files.length === 0) return true;

      const allowedMimes = ZkValidatorUtils.getMimeType(allowedExts);
      for (let i = 0; i < element.files.length; i++) {
        if (!allowedMimes.includes(element.files[i].type)) {
          return false;
        }
      }
      return true;
    },
    message: function (element, message = "", ...mimes) {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.mimes)
        .replace(/:field|:attribute/g, label)
        .replace(":values", mimes.join(", "));
    },
  },
  uuid: {
    handler(element) {
      // Allow empty input or check if the value is a valid UUID
      if (ZkValidatorUtils.isEmpty(element.value)) return true;
      const uuidPattern = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
      return uuidPattern.test(element.value);
    },
    message(element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.uuid)
        .replace(/:field|:attribute/g, label);
    }
  },
  server: {
    handler: async function (element, url, ...args) {
      // Allow empty input
      if (ZkValidatorUtils.isEmpty(element.value)) return true;

      // Abort previous request if needed
      if (element._serverAbortController) {
        element._serverAbortController.abort();
      }
      const controller = new AbortController();
      element._serverAbortController = controller;

      let body = new URLSearchParams({ [element.name]: element.value });
      if (args.length > 0) {
        args.forEach((arg, i) => {
          body.append(i, arg);
        });
      }

      ZkValidatorUtils.dispatchCustomEvent(element, "zk-server-loading", {
        url,
        body: body.toString(),
      });
      element.classList.add("zk-server-loading");
      try {
        const response = await fetch(url, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: body.toString(),
          signal: controller.signal,
        });
        const data = await response.json();
        ZkValidatorUtils.dispatchCustomEvent(element, "zk-server-success", {
          valid: data.valid,
          url,
          body: body.toString(),
        });
        return data.valid;
      } catch (err) {
        if (err.name === "AbortError") return false;
        ZkValidatorUtils.dispatchCustomEvent(element, "zk-server-error", {
          error,
          url,
          body: body.toString(),
        });
        return false;
      } finally {
        ZkValidatorUtils.dispatchCustomEvent(element, "zk-server-complete", {
          url,
          body: body.toString(),
        });
        element.classList.remove("zk-server-loading");
        element._serverAbortController = null;
      }
    },
    message: function (element, message = "") {
      const label = ZkValidatorUtils.getLabelText(element);
      return (message || ZkValidatorMessages.server)
        .replace(/:field|:attribute/g, label);
    },
  },
};

export class ZkFormValidator {
  options = {
    position: "afterend", // beforebegin, afterbegin, beforeend, afterend
    error: {
      group: { class: "", tag: "" }, // default empty, can be 'div'
      class: "invalid-feedback",
      tag: "span",
    },
    input: {
      errorClass: "is-invalid",
      successClass: "is-valid",
    },
    inputGroup: {
      selector: ".form-group",
      errorClass: "has-error",
      successClass: "has-success",
    },
    exclude: ["submit", "button", "reset", "hidden", ":disabled"],
  };

  isValid = true;

  constructor(form, fields = {}, rules = {}, messages = {}) {
    this.form = typeof form === "string" ? document.getElementById(form) : form;
    if (!this.form || this.form.tagName !== "FORM") {
      console.error("Invalid form element provided.");
      return;
    }
    this.form.noValidate = true;
    this.validator = new ZkValidator(fields, rules, messages);
    this.fieldSelector = "input, select, textarea, [contenteditable]";
    this.init();
  }

  setOptions(options) {
    this.options = { ...this.options, ...options };
  }

  getOptions() {
    return this.options;
  }

  getValidator() {
    return this.validator;
  }

  setCheckAllRules(checkAllRules) {
    this.validator.setCheckAllRules(checkAllRules);
  }

  initFieldSelector() {
    if (this.options.exclude.length) {
      const inputNotSelector = [],
        attrNotSelector = [];
      for (const attr of this.options.exclude) {
        if (attr.startsWith(":")) {
          attrNotSelector.push(attr);
          inputNotSelector.push(attr);
        } else {
          inputNotSelector.push(`[type="${attr}"]`);
        }
      }
      this.fieldSelector = this.fieldSelector
        .split(", ")
        .map((field) => {
          return field === "input"
            ? `${field}:not(${inputNotSelector.join(", ")})`
            : `${field}:not(${attrNotSelector.join(", ")})`;
        })
        .join(", ");
    }
  }

  async init() {
    this.initFieldSelector();
    this.addContextFields(this.form);
  }

  setupFieldRulesFromData(element) {
    const rules = JSON.parse(element.dataset.rules);
    const messages = JSON.parse(element.dataset.messages || "{}");
    this.validator.addField(element.name, rules, messages);
    return true;
  }

  setupFieldRulesFromAttributes(element) {
    const rules = [];
    const messages = {};
    const typeRule = this.validator.rules[element.type] ? element.type : null;

    Array.from(element.attributes).forEach((attr) => {
      if (this.validator.rules[attr.name]) {
        rules.push(`${attr.name}:${attr.value}`);
      }
      if (attr.name === "required" && typeRule) {
        rules.push(typeRule);
      }
      if (/data-\w+-message/.test(attr.name)) {
        const rule = attr.name.replace("data-", "").replace("-message", "");
        if (this.validator.rules[rule]) {
          messages[rule] = attr.value;
        }
      }
    });

    if (rules.length) {
      this.validator.addField(element.name, rules, messages);
      return true;
    }

    return false;
  }

  addContextFields(context) {
    context.querySelectorAll(this.fieldSelector).forEach((element) => {
      const fieldRulesSetup = element.dataset.rules
        ? this.setupFieldRulesFromData
        : this.setupFieldRulesFromAttributes;

      if (fieldRulesSetup.call(this, element)) {
        const field = this.getField(element.name);
        if (field) {
          this.registerFieldEvents(element, field.rules, field.messages);
        }
      }
    });
  }

  addField(name, rules = {}, messages = {}) {
    const element = this.getElement(name);
    if (!element) return;
    if (rules && Object.keys(rules).length) {
      this.validator.addField(element.name, rules, messages);
    } else {
      if (element.dataset.rules) {
        this.setupFieldRulesFromData(element);
      } else {
        this.setupFieldRulesFromAttributes(element);
      }
    }
    this.registerFieldEvents(element, rules, messages);
  }

  addFields(fields) {
    for (const name in fields) {
      const field = fields[name];
      if (field.rules) {
        this.addField(name, field.rules, field.messages ?? {});
      } else {
        this.addField(name, field);
      }
    }
  }

  removeContextFields(context) {
    context.querySelectorAll("input, select, textarea").forEach((element) => {
      this.removeField(element.name);
    });
  }

  removeField(name) {
    this.validator.removeField(name);
  }

  removeAllFields() {
    this.validator.removeAllFields();
  }

  getElement(element) {
    return this.validator.getElement(element);
  }

  registerFieldEvents(element, rules, messages) {

    if (!this._debouncedListeners) {
      this._debouncedListeners = new WeakMap();
    }

    const events = ["checkbox", "radio"].includes(element.type)
      ? ["click", "change"]
      : ["change", "keyup"];

    if (!this._debouncedListeners.has(element)) {
      this._debouncedListeners.set(element, {});
    }

    const listenerMap = this._debouncedListeners.get(element);

    events.forEach((event) => {
      // If we previously added a listener, remove it
      if (listenerMap[event]) {
        element.removeEventListener(event, listenerMap[event]);
      }

      // Create new listener (debounced if needed)
      const rawListener = async (e) => {
        await this.validateField(element, rules, messages);
      };
      // If the event is "keyup", debounce the listener
      const finalListener = event === "keyup"
        ? this.debounce(rawListener, 500)
        : rawListener;

      // Store for future reference/removal
      listenerMap[event] = finalListener;

      // Add listener
      element.addEventListener(event, finalListener);
    });
  }

  async validateField(element, rules, messages = {}) {
    const isValidField = await this.validator.validateField(
      element,
      rules,
      messages
    );
    this.clearError(element);

    if (!isValidField) {
      this.isValid = false;
      this.showError(element, this.validator.getErrors()[element.name]);
    } else {
      this.validField(element);
    }
    return isValidField;
  }

  async validate(fields = {}) {
    this.clearErrors();
    this.validator.errors = {};
    this.isValid = true;
    const _fields = { ...this.validator.fields, ...fields };
    for (const name in _fields) {
      const field = _fields[name];
      const element = this.getElement(name);
      if (element) {
        const isValidField = await this.validateField(
          element,
          field.rules || field,
          field.messages || {}
        );
        if (!isValidField) this.isValid = false;
      }
    }

    if (!this.isValid) {
      const focusElement = this.form.querySelector(
        `.${this.options.input.errorClass}`
      );
      if (focusElement) focusElement.focus();
    }

    return this.isValid;
  }

  async onlyValidate(fields) {
    this.clearErrors();
    this.validator.errors = {};
    this.isValid = true;
    for (const name of fields) {
      const field = this.validator.fields[name];
      const element = this.getElement(name);
      if (element) {
        const isValidField = await this.validateField(
          element,
          field.rules || field,
          field.messages || {}
        );
        if (!isValidField) this.isValid = false;
      }
    }

    if (!this.isValid) {
      const focusElement = this.form.querySelector(
        `.${this.options.input.errorClass}`
      );
      if (focusElement) focusElement.focus();
    }

    return this.isValid;
  }


  isInvalid() {
    return !this.isValid;
  }

  getErrors() {
    return this.validator.getErrors();
  }

  getFields() {
    return this.validator.getFields();
  }

  getField(name) {
    return this.validator.getField(name);
  }

  showError(element, errors) {
    // Clear previous success class
    if (this.options.input.successClass)
      element.classList.remove(this.options.input.successClass);
    if (this.options.inputGroup.selector)
      element
        .closest(this.options.inputGroup.selector)
        ?.classList.remove(this.options.inputGroup.successClass);

    let errorElement =
      this.options.error.group && this.options.error.group.tag
        ? document.createElement(this.options.error.group.tag)
        : null;

    errorElement && errorElement.classList.add(this.options.error.group.class);

    for (const name in errors) {
      const error = document.createElement(this.options.error.tag);
      error.className = this.options.error.class;
      error.id = `${element.id}-error-${name}`;
      error.innerHTML = errors[name];
      errorElement
        ? errorElement.appendChild(error)
        : element.insertAdjacentElement(this.options.position, error);
    }
    if (this.options.input.errorClass)
      element.classList.add(this.options.input.errorClass);
    if (this.options.inputGroup.selector)
      element
        .closest(this.options.inputGroup.selector)
        ?.classList.add(this.options.inputGroup.errorClass);

    errorElement &&
      element.insertAdjacentElement(this.options.position, errorElement);
  }

  validField(element) {
    this.clearError(element);
    if (this.options.input.successClass)
      element.classList.add(this.options.input.successClass);
    if (this.options.inputGroup.selector)
      element
        .closest(this.options.inputGroup.selector)
        ?.classList.add(this.options.inputGroup.successClass);
  }

  clearError(element) {
    if (this.options.input.errorClass)
      element.classList.remove(this.options.input.errorClass);
    if (this.options.inputGroup.selector)
      element
        .closest(this.options.inputGroup.selector)
        ?.classList.remove(this.options.inputGroup.errorClass);

    const siblingElements = [element.nextElementSibling];
    if (this.options.error.group && this.options.error.group.tag) {
      if (
        siblingElements[0] &&
        siblingElements[0].classList.contains(this.options.error.group.class)
      ) {
        siblingElements[0].remove();
      }
    } else {
      while (
        siblingElements[0] &&
        siblingElements[0].classList.contains(this.options.error.class)
      ) {
        siblingElements[0].remove();
        siblingElements[0] = element.nextElementSibling;
      }
    }
  }

  clearErrors() {
    this.isValid = true;
    this.validator.errors = {};

    if (this.options.error.group.class)
      this.form
        .querySelectorAll(`.${this.options.error.group.class}`)
        .forEach((el) => el.remove());
    if (this.options.error.class)
      this.form
        .querySelectorAll(`.${this.options.error.class}`)
        .forEach((el) => el.remove());
    if (this.options.input.errorClass)
      this.form
        .querySelectorAll(`.${this.options.input.errorClass}`)
        .forEach((el) => el.classList.remove(this.options.input.errorClass));
    if (this.options.input.successClass)
      this.form
        .querySelectorAll(`.${this.options.input.successClass}`)
        .forEach((el) => el.classList.remove(this.options.input.successClass));
    if (this.options.inputGroup.errorClass)
      this.form
        .querySelectorAll(`.${this.options.inputGroup.errorClass}`)
        .forEach((el) =>
          el.classList.remove(this.options.inputGroup.errorClass)
        );
    if (this.options.inputGroup.successClass)
      this.form
        .querySelectorAll(`.${this.options.inputGroup.successClass}`)
        .forEach((el) =>
          el.classList.remove(this.options.inputGroup.successClass)
        );
  }

  reset() {
    this.clearErrors();
  }

  debounce(func, delay) {
    let timeout;
    return function (...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), delay);
    };
  }
}

export default class ZkValidator {
  static ZkValidatorMessages = ZkValidatorMessages;
  static ZkValidatorRules = ZkValidatorRules;
  static ZkFormValidator = ZkFormValidator;
  static ZkValidatorUtils = ZkValidatorUtils;

  constructor(fields = {}, rules = {}, messages = {}) {
    this.checkAllRules = false;
    this.fields = fields;
    this.rules = { ...ZkValidator.ZkValidatorRules, ...rules };
    this.messages = { ...ZkValidator.ZkValidatorMessages, ...messages };
    this.utils = ZkValidator.ZkValidatorUtils;
    this.errors = {};
  }

  setCheckAllRules(checkAllRules) {
    this.checkAllRules = checkAllRules;
  }

  isCheckAllRules() {
    return this.checkAllRules;
  }

  addUtility(name, utility) {
    this.utils[name] = utility;
  }

  addUtilities(utilities) {
    for (const name in utilities) {
      this.addUtility(name, utilities[name]);
    }
  }

  getUtility() {
    return this.utils;
  }

  addRule(name, handler, message) {
    this.rules[name] = { handler, message };
  }

  addRules(rules) {
    for (const name in rules) {
      this.addRule(name, rules[name].handler, rules[name].message);
    }
  }

  addMessage(name, message) {
    this.messages[name] = message;
  }

  addMessages(messages) {
    for (const name in messages) {
      this.addMessage(name, messages[name]);
    }
  }

  addField(name, rules, messages = {}) {
    if (Array.isArray(rules)) {
      rules = rules.join("|");
    }

    if (typeof rules === "object" && (rules.rules || rules.messages)) {
      messages = rules.messages || {};
      rules = rules.rules || {};
    }

    if (typeof rules === "string") {
      const rulesArray = rules.split("|");
      rules = {};
      rulesArray.forEach((rule) => {
        const [ruleName, ...ruleArgs] = rule.split(":");
        rules[ruleName] = ruleArgs.join(":") || "true";
      });
    }

    this.fields[name] = { rules, messages };
  }

  addFields(fields) {
    for (const name in fields) {
      const field = fields[name];
      if (field.rules) {
        this.addField(name, field.rules, field.messages ?? {});
      } else {
        this.addField(name, field);
      }
    }
  }

  removeField(name) {
    if (this.fields[name]) delete this.fields[name];
  }

  removeAllFields() {
    this.fields = {};
  }

  async validateField(element, rules, messages = {}) {
    let isValid = true;
    element = this.getElement(element);
    if (!element) {
      console.error("Element not found");
      return true;
    }
    this.clearFieldErrors(element.name);
    for (const rule in rules) {
      if (!this.rules[rule]) {
        console.error(`Validation rule "${rule}" not defined.`);
        continue;
      }
      const args = rules[rule].split(",");
      const ruleResult = await this.executeRule(rule, element, args);
      if (!ruleResult) {
        isValid = false;
        const _message = messages[rule] || this.messages[rule] || "";
        const message = (this.rules[rule].message && typeof this.rules[rule].message === "function")
          ? this.rules[rule].message(element, _message, ...args)
          : _message;
        this.errors[element.name] = {
          ...(this.errors[element.name] || {}),
          [rule]: message,
        };
        if (!this.checkAllRules) break;
      } else {
        if (this.errors[element.name]) delete this.errors[element.name][rule];
      }
    }

    return isValid;
  }

  async validate(fields = {}) {
    this.errors = {};
    let isValid = true;
    const _fields = { ...this.fields, ...fields };
    for (const name in _fields) {
      const element = this.getElement(name);
      if (!element) continue;
      const field = _fields[name];
      const fieldValid = await this.validateField(
        element,
        field.rules || field,
        field.messages || {}
      );
      if (!fieldValid) isValid = false;
    }
    return isValid;
  }

  async onlyValidate(fields) {
    this.errors = {};
    let isValid = true;
    for (const name of fields) {
      const element = this.getElement(name);
      if (!element) continue;
      const field = this.fields[name];
      const fieldValid = await this.validateField(
        element,
        field.rules || field,
        field.messages || {}
      );
      if (!fieldValid) isValid = false;
    }
    return isValid;
  }

  getErrors() {
    return this.errors;
  }

  getRules() {
    return this.rules;
  }

  getMessages() {
    return this.messages;
  }

  getFields() {
    return this.fields;
  }

  getField(name) {
    return this.fields[name] || null;
  }

  getElement(element) {
    return typeof element === "string"
      ? document.querySelector(`[name="${element}"]`) || document.getElementById(element)
      : element;
  }

  clearFieldErrors(fieldName) {
    if (this.errors[fieldName]) delete this.errors[fieldName];
  }

  async executeRule(rule, element, args) {
    const ruleHandler = this.rules[rule].handler;
    let result = ruleHandler(element, ...args);
    return (result && typeof result.then === "function") ||
      result instanceof Promise
      ? await result
      : result;
  }
}