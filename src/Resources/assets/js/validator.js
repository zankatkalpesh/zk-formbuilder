export const ZkValidatorUtils = {
  // Check if the value is empty
  isEmpty(value) {
    return value === null || value === undefined || value.trim() === "";
  },
  // Check if the value is a number
  isNumber(value) {
    return !isNaN(value) && !isNaN(parseFloat(value));
  },
  // Condition Checking
  checkCondition(matchValue, operator, value) {
    switch (operator) {
      case "=":
        return matchValue == value;
      case "==":
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
  // Valid MIME types
  validMimeTypes: {
    // Get valid mime types for the specified file extension
    pdf: "application/pdf",
    doc: "application/msword",
    docx: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    xls: "application/vnd.ms-excel",
    xlsx: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    ppt: "application/vnd.ms-powerpoint",
    pptx: "application/vnd.openxmlformats-officedocument.presentationml.presentation",
    txt: "text/plain",
    rtf: "application/rtf",
    odt: "application/vnd.oasis.opendocument.text",
    ods: "application/vnd.oasis.opendocument.spreadsheet",

    // Images
    jpg: "image/jpeg",
    jpeg: "image/jpeg",
    png: "image/png",
    gif: "image/gif",
    tiff: "image/tiff",
    bmp: "image/bmp",
    webp: "image/webp",
    svg: "image/svg+xml",
    ico: "image/vnd.microsoft.icon",

    // Audio
    mp3: "audio/mpeg",
    wav: "audio/wav",
    ogg: "audio/ogg",
    aac: "audio/aac",
    flac: "audio/flac",

    // Video
    mp4: "video/mp4",
    webm: "video/webm",
    avi: "video/x-msvideo",
    mov: "video/quicktime",
    mkv: "video/x-matroska",
    flv: "video/x-flv",
    ogv: "video/ogg",

    // Archives
    // zip: "application/zip",
    // rar: "application/x-rar-compressed",
    // "7z": "application/x-7z-compressed",
    // tar: "application/x-tar",

    // Code and Text Files
    // html: "text/html",
    // css: "text/css",
    // js: "application/javascript",
    // json: "application/json",
    // xml: "application/xml",
    csv: "text/csv",
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
        return new Date(now.setDate(now.getDate() + 1)); // Tomorrow at 00:00:00
      case 'week':
        return new Date(now.setDate(now.getDate() + 7)); // One week from now at 00:00:00
      default:
        // Parse the default date according to the format
        return ZkValidatorUtils.parseDate(date, format);
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

    // Return the constructed date object
    return {
      year: parseInt(year),
      month: parseInt(month),
      day: parseInt(day),
      date: new Date(year, month - 1, day),
    }
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
  required_if: "This field is required when :field is :value.",
  email: "Invalid email address.",
  numeric: "This field must be numeric.",
  integer: "This field must be an integer.",
  alpha: "This field must be alphabetic.",
  alpha_num: "This field must be alphanumeric.",
  alpha_dash: "This field may only contain letters, numbers, and dashes.",
  url: "This field is not a valid URL.",
  date: "This field is not a valid date.",
  date_before: "This field must be a date before :date.",
  date_after: "This field must be a date after :date.",
  date_format: "This field must be a date in the format :format.",
  contains: "This field must contain :contains.",
  boolean: "This field must be true or false.",
  minlength: "This field must be at least :length characters.",
  maxlength: "This field may not be greater than :length characters.",
  starts_with: "This field must start with :prefix.",
  ends_with: "This field must end with :suffix.",
  in: "This field must be one of the following: :values.",
  not_in: "This field must not be one of the following: :values.",
  match: "This field must match the field :field.",
  pattern: "This field format is invalid.",
  regex: "This field format is invalid.",
  not_regex: "This field format is invalid.",
  min: "This field must be greater than or equal to :min.",
  max: "This field must be less than or equal to :max.",
  between: "This field must be between :min and :max.",
  size: "This file size must be equal to :size.",
  min_size: "This file size must be greater than or equal to :size.",
  max_size: "This file size must be less than or equal to :size.",
  between_size: "This file size must be between :min and :max.",
  mimes: "This field must have a valid file :values.",
  unique: "This field must be unique.",
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
      // Check if value is empty or null
      return element.value === null || element.value === undefined || element.value.trim() === '';
    },
    message: function (element, message = "") {
      return message;
    },
  },
  required: {
    handler: function (element) {
      // Check checkbox, radio, select, file, and other input types
      if (["checkbox", "radio"].includes(element.type)) {
        const inputs = document.getElementsByName(element.name);
        return Array.from(inputs).some((input) => input.checked);
      } else if (element.type === "file") {
        return element.files.length > 0;
      } else if (element.tagName === "SELECT") {
        // Check multiple select and check if at least one option is selected and value is not empty
        if (element.multiple) {
          return Array.from(element.options).some(
            (option) => option.selected && option.value.trim() !== ""
          );
        }
      }
      return element.value.trim() !== "";
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.required;
    },
  },
  required_if: {
    handler: function (element, field, value) {
      // Check operator include =, !=, >, <, >=, and <=
      if (operator && !["=", "==", "!=", ">", "<", ">=", "<="].includes(operator)) {
        value = operator;
        operator = "=";
      }
      const _self = ZkValidatorRules;
      const matchField =
        document.getElementById(field) ||
        document.querySelector(`[name="${field}"]`);
      if (matchField == null) {
        return false;
      }
      let isRequired = false;
      // Check checkbox, radio, select and other input types
      if (["checkbox", "radio"].includes(matchField.type)) {
        const inputs = document.getElementsByName(matchField.name);
        isRequired = Array.from(inputs).some(
          (input) => input.checked && ZkValidatorUtils.checkCondition(input.value, operator, value)
        );
      } else if (matchField.type === "file") {
        isRequired = matchField.files.length > 0;
      } else if (matchField.tagName === "SELECT") {
        // Check multiple select and check if at least one option is selected and value is not empty
        isRequired = Array.from(matchField.options).some(
          (option) => option.selected && ZkValidatorUtils.checkCondition(option.value.trim(), operator, value)
        );
      } else {
        isRequired =
          (value === "" || value === undefined || value === null) &&
          matchField.value.trim() !== "";
        if (value !== "" && ZkValidatorUtils.checkCondition(matchField.value.trim(), operator, value)) {
          isRequired = true;
        }
      }
      return isRequired ? _self.required.handler(element) : true;
    },
    message: function (element, message = "", field, value) {
      const matchField =
        document.getElementById(field) ||
        document.querySelector(`[name="${field}"]`);
      if (matchField) {
        // get label text
        const label = matchField.closest("label");
        if (label) {
          field = label.innerText;
        } else {
          field = matchField.name;
        }
      }
      return message || ZkValidatorMessages.required_if.replace(":field", field).replace(":value", value);
    },
  },
  email: {
    handler: function (element) {
      // Check if value is empty or match email pattern
      return (
        element.value.trim() === "" ||
        element.value.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/)
      );
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.email;
    },
  },
  numeric: {
    handler: function (element) {
      // Check if value is empty or match numeric pattern
      return (
        element.value.trim() === "" || element.value.match(/^-?\d*\.?\d+$/)
      );
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.numeric;
    },
  },
  integer: {
    handler: function (element) {
      // Check if value is empty or match integer pattern
      return element.value.trim() === "" || element.value.match(/^-?\d+$/);
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.integer;
    },
  },
  alpha: {
    handler: function (element) {
      // Check if value is empty or match alphabetic pattern
      return element.value.trim() === "" || element.value.match(/^[a-zA-Z]+$/);
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.alpha;
    },
  },
  alpha_num: {
    handler: function (element) {
      // Check if value is empty or match alphanumeric pattern
      return (
        element.value.trim() === "" || element.value.match(/^[a-zA-Z0-9]+$/)
      );
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.alpha_num;
    },
  },
  alpha_dash: {
    handler: function (element) {
      // Check if value is empty or match alpha dash pattern
      return (
        element.value.trim() === "" || element.value.match(/^[a-zA-Z0-9_-]+$/)
      );
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.alpha_dash;
    },
  },
  url: {
    handler: function (element) {
      try {
        new URL(element.value.trim());
        return true;
      } catch {
        if (element.value.trim() === "") return true;
        return false;
      }
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.url;
    },
  },
  date: {
    handler: function (element, format = "YYYY-MM-DD") {
      const value = element.value.trim();
      if (!value) return false;
      // Check format is true or not
      format = format === "true" ? "YYYY-MM-DD" : format;

      const parseDate = ZkValidatorUtils.parseDate(value, format);
      if (!parseDate || isNaN(parseDate.date.getTime())) return false;

      // Check if the date is valid
      const { year, month, day, date } = parseDate;
      const isValidDate = date.getFullYear() === year &&
        date.getMonth() + 1 === month &&
        date.getDate() === day;
      // Check if the date is valid
      return isValidDate;
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.date;
    },
  },
  date_before: {
    handler: function (element, date, format = "YYYY-MM-DD") {
      // Check if value is empty or is a date before the specified date
      const inputValue = element.value.trim();
      if (!inputValue) return true; // Return true for empty input (no validation required)

      const inputDate = ZkValidatorUtils.parseDate(inputValue, format);
      if (!inputDate || isNaN(inputDate.date.getTime())) return false;

      const compareDate = ZkValidatorUtils.getComparisonDate(date);
      if (!compareDate || isNaN(compareDate.getTime())) return false;

      inputDate.date.setHours(0, 0, 0, 0);
      compareDate.setHours(0, 0, 0, 0);

      // Check if inputDate is strictly before compareDate
      return inputDate.date.getTime() < compareDate.getTime();
    },
    message: function (element, message = "", date) {
      message = message || ZkValidatorMessages.date_before;
      return message.replace(":date", date);
    },
  },
  date_after: {
    handler: function (element, date, format = "YYYY-MM-DD") {
      // Check if value is empty or match date after pattern
      const inputValue = element.value.trim();
      if (!inputValue) return true; // Return true for empty input (no validation required)

      const inputDate = ZkValidatorUtils.parseDate(inputValue, format);
      if (!inputDate || isNaN(inputDate.date.getTime())) return false;

      const compareDate = ZkValidatorUtils.getComparisonDate(date);
      if (!compareDate || isNaN(compareDate.getTime())) return false;

      inputDate.date.setHours(0, 0, 0, 0);
      compareDate.setHours(0, 0, 0, 0);

      // Check if inputDate is strictly after compareDate
      return inputDate.date.getTime() > compareDate.getTime();
    },
    message: function (element, message = "", date) {
      message = message || ZkValidatorMessages.date_after;
      return message.replace(":date", date);
    },
  },
  date_format: {
    handler: function (element, format) {
      const inputValue = element.value.trim();
      if (!inputValue) return true; // Return true for empty input (no validation required)

      const parseDate = ZkValidatorUtils.parseDate(inputValue, format);
      if (!parseDate || isNaN(parseDate.date.getTime())) return false;

      const { year, month, day, date } = parseDate;

      const isValidDate =
        date.getFullYear() === year &&
        date.getMonth() + 1 === month &&
        date.getDate() === day;

      return isValidDate;
    },
    message: function (element, message = "", format) {
      message = message || ZkValidatorMessages.date_format;
      return message.replace(":format", format);
    },
  },
  contains: {
    handler: function (element, value) {
      // Check if value is empty or contains the specified value
      return element.value.trim() === "" || element.value.includes(value);
    },
    message: function (element, message = "", value) {
      message = message || ZkValidatorMessages.contains;
      return message.replace(":contains", value);
    },
  },
  boolean: {
    handler: function (element) {
      // Check if value is empty or is a boolean value
      return [true, false, 'true', 'false', 0, 1, '0', '1'].includes(element.value.trim());
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.boolean;
    },
  },
  minlength: {
    handler: function (element, length) {
      // Check if value is empty or length is greater than or equal to the specified length
      return element.value.trim() === "" || element.value.length >= length;
    },
    message: function (element, message = "", length) {
      message = message || ZkValidatorMessages.minlength;
      return message.replace(":length", length);
    },
  },
  maxlength: {
    handler: function (element, length) {
      // Check if value is empty or length is less than or equal to the specified length
      return element.value.trim() === "" || element.value.length <= length;
    },
    message: function (element, message = "", length) {
      message = message || ZkValidatorMessages.maxlength;
      return message.replace(":length", length);
    },
  },
  starts_with: {
    handler: function (element, prefix) {
      // Check if value is empty or starts with the specified prefix
      return element.value.trim() === "" || element.value.startsWith(prefix);
    },
    message: function (element, message = "", prefix) {
      message = message || ZkValidatorMessages.starts_with;
      return message.replace(":prefix", prefix);
    },
  },
  ends_with: {
    handler: function (element, suffix) {
      // Check if value is empty or ends with the specified suffix
      return element.value.trim() === "" || element.value.endsWith(suffix);
    },
    message: function (element, message = "", suffix) {
      message = message || ZkValidatorMessages.ends_with;
      return message.replace(":suffix", suffix);
    },
  },
  in: {
    handler: function (element, ...values) {
      // Check if value is empty or is in the specified values
      return element.value.trim() === "" || values.includes(element.value);
    },
    message: function (element, message = "", ...values) {
      message = message || ZkValidatorMessages.in;
      return message.replace(":values", values.join(", "));
    },
  },
  not_in: {
    handler: function (element, ...values) {
      // Check if value is empty or is not in the specified values
      return element.value.trim() === "" || !values.includes(element.value);
    },
    message: function (element, message = "", values) {
      message = message || ZkValidatorMessages.not_in;
      return message.replace(":values", values.join(", "));
    },
  },
  match: {
    handler: function (element, field) {
      // match the value of the specified field
      const matchField = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
      if (matchField == null) {
        return false;
      }
      return element.value === matchField.value;
    },
    message: function (element, message = "", field) {
      message = message || ZkValidatorMessages.match;
      return message.replace(":field", field);
    },
  },
  pattern: {
    handler: function (element, pattern) {
      // Check if value is empty or match the specified pattern
      const regex = new RegExp(pattern);
      return element.value.trim() === "" || regex.test(element.value);
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.pattern;
    },
  },
  regex: {
    handler: function (element, pattern) {
      // Check if value is empty or match the specified regex pattern - remove slashes from regex
      const regex = new RegExp(pattern.replace(/^\/|\/$/g, ""));
      return element.value.trim() === "" || regex.test(element.value);
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.regex;
    },
  },
  not_regex: {
    handler: function (element, pattern) {
      // Check if value is empty or does not match the specified regex pattern - remove slashes from regex
      const regex = new RegExp(pattern.replace(/^\/|\/$/g, ""));
      return element.value.trim() === "" || !regex.test(element.value);
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.not_regex;
    },
  },
  min: {
    handler: function (element, min) {
      // Check checkbox, select and file
      if (["checkbox"].includes(element.type)) {
        const inputs = document.getElementsByName(element.name);
        return Array.from(inputs).filter((input) => input.checked).length >= min;
      } else if (element.tagName === "SELECT") {
        // Check multiple select and check if at least the specified min options are selected
        if (element.multiple) {
          return Array.from(element.options).filter(
            (option) => option.selected && option.value.trim() !== ""
          ).length >= min;
        }
      } else if (element.type === "file") {
        return element.files.length >= min;
      }
      // Check if value is empty or is greater than or equal to the specified min value
      return element.value.trim() === "" || parseFloat(element.value) >= min;
    },
    message: function (element, message = "", min) {
      message = message || ZkValidatorMessages.min;
      return message.replace(":min", min);
    },
  },
  max: {
    handler: function (element, max) {
      // Check checkbox, select and file
      if (["checkbox"].includes(element.type)) {
        const inputs = document.getElementsByName(element.name);
        return Array.from(inputs).filter((input) => input.checked).length <= max;
      } else if (element.tagName === "SELECT") {
        // Check multiple select and check if at most the specified max options are selected
        if (element.multiple) {
          return Array.from(element.options).filter(
            (option) => option.selected && option.value.trim() !== ""
          ).length <= max;
        }
      } else if (element.type === "file") {
        return element.files.length <= max;
      }
      // Check if value is empty or is less than or equal to the specified max value
      return element.value.trim() === "" || parseFloat(element.value) <= max;
    },
    message: function (element, message = "", max) {
      message = message || ZkValidatorMessages.max;
      return message.replace(":max", max);
    },
  },
  between: {
    handler: function (element, min, max) {
      // Check checkbox, select and file
      if (["checkbox"].includes(element.type)) {
        const inputs = document.getElementsByName(element.name);
        const checked = Array.from(inputs).filter((input) => input.checked).length;
        return checked >= min && checked <= max;
      } else if (element.tagName === "SELECT") {
        // Check multiple select and check if the selected options are between the specified min and max values
        if (element.multiple) {
          const selected = Array.from(element.options).filter(
            (option) => option.selected && option.value.trim() !== ""
          ).length;
          return selected >= min && selected <= max;
        }
      } else if (element.type === "file") {
        return element.files.length >= min && element.files.length <= max;
      }
      // Check if value is empty or is between the specified min and max values
      return (
        element.value.trim() === "" ||
        (parseFloat(element.value) >= min && parseFloat(element.value) <= max)
      );
    },
    message: function (element, message = "", min, max) {
      message = message || ZkValidatorMessages.between;
      return message.replace(":min", min).replace(":max", max);
    },
  },
  size: {
    handler: function (element, size) {
      // Check if value is empty or file size is equal to the specified size
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
      message = message || ZkValidatorMessages.size;
      return message.replace(":size", size);
    },
  },
  min_size: {
    handler: function (element, size) {
      // Check if value is empty or file size is greater then the specified size
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
      message = message || ZkValidatorMessages.min_size;
      return message.replace(":size", size);
    },
  },
  max_size: {
    handler: function (element, size) {
      // Check if value is empty or file size is less than the specified size
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
      message = message || ZkValidatorMessages.max_size;
      return message.replace(":size", size);
    },
  },
  between_size: {
    handler: function (element, min, max) {
      // Check if value is empty or file size is between the specified min and max values
      if (element.files.length === 0) return true;
      const minSize = parseFloat(min); // in KB
      const maxSize = parseFloat(max); // in KB
      for (let i = 0; i < element.files.length; i++) {
        const sizeInKB = parseFloat((element.files[i].size / 1024).toFixed(2));
        // Check if the file size is between the specified min and max values
        if (sizeInKB < minSize || sizeInKB > maxSize) {
          return false;
        }
      }
      return true;
    },
    message: function (element, message = "", min, max) {
      message = message || ZkValidatorMessages.between_size;
      return message.replace(":min", min).replace(":max", max);
    },
  },
  mimes: {
    handler: function (element, ...mimes) {
      // Check if value is empty or file type is in the specified mimes
      if (element.files.length === 0) return true;
      const validMimes = [];
      mimes.forEach((mime) => {
        const fileMime = ZkValidatorUtils.validMimeTypes[mime] ?? null;
        if (fileMime) {
          validMimes.push(fileMime);
        }
      });
      if (element.files.length > 1) {
        for (let i = 0; i < element.files.length; i++) {
          if (!validMimes.includes(element.files[i].type)) {
            return false;
          }
        }
        return true;
      }
      return validMimes.includes(element.files[0].type);
    },
    message: function (element, message = "", ...mimes) {
      message = message || ZkValidatorMessages.mimes;
      return message.replace(":values", mimes.join(", "));
    },
  },
  unique: {
    handler: async function (element, url, ...args) {
      // Check if value is empty or is unique
      if (element.value.trim() === "") return true;
      let body = new URLSearchParams({ [element.name]: element.value });
      if (args.length > 0) {
        args.forEach((arg, i) => {
          body.append(i, arg);
        });
      }
      ZkValidatorUtils.dispatchCustomEvent(element, "zk-unique-loading", {
        url,
        body: body.toString(),
      });
      element.classList.add("zk-unique-loading");
      try {
        const response = await fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: body.toString(),
        });
        const data = await response.json();
        ZkValidatorUtils.dispatchCustomEvent(element, "zk-unique-success", {
          isValid: data.valid,
          url,
          body: body.toString(),
        });
        return data.valid;
      } catch (error) {
        ZkValidatorUtils.dispatchCustomEvent(element, "zk-unique-error", {
          error,
          url,
          body: body.toString(),
        });
        return false;
      } finally {
        ZkValidatorUtils.dispatchCustomEvent(element, "zk-unique-complete", {
          url,
          body: body.toString(),
        });
        element.classList.remove("zk-unique-loading");
      }
    },
    message: function (element, message = "") {
      return message || ZkValidatorMessages.unique;
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
      console.error("Form element not found");
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
    const events = ["checkbox", "radio"].includes(element.type)
      ? ["click", "change"]
      : ["change", "keyup"];
    const listener = async (event) => {
      await this.validateField(element, rules, messages);
    };
    events.forEach((event) => {
      const _listener =
        event === "keyup" ? this.debounce(listener, 500) : listener;
      element.removeEventListener(event, _listener);
      element.addEventListener(event, _listener);
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
    this.errors = {};
    this.utils = ZkValidator.ZkValidatorUtils;
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
        console.error(`Rule "${rule}" is not defined`);
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
