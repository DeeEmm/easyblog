EasyBlog.require()
.done(function($) {

    var textareaInput = $('[data-field-class-input-textarea]');
    var textareaWrapper = $('[data-field-type-textarea]');

    // textarea input field
    textareaInput.on('keyup', function(e) {

        // check the current input value
        var currentValue = textareaInput.val();

        // add the hide class name if empty character
        if ($.isEmpty(currentValue)) {
            textareaWrapper.addClass('hide');
        
        } else {
            // if more than one character then only populate that field
            textareaWrapper.removeClass('hide');
        }
    });

    var textInput = $('[data-field-class-input-text]');
    var textWrapper = $('[data-field-type-text]');

    // text input field
    textInput.on('keyup', function(e) {

        // check the current text value
        var currentText = textInput.val();

        // add the hide class name if doesn't enter any text
        if ($.isEmpty(currentText)) {
            textWrapper.addClass('hide');
        
        } else {
            // if more than one character then only populate that field
            textWrapper.removeClass('hide');
        };   
    });

    var hyperlinkInput = $('[data-field-class-input-hyperlink]');
    var hyperlinkWrapper = $('[data-field-type-hyperlink]');

    // hyperlink input field
    hyperlinkInput.on('keyup', function(e) {

        // check the current text and link value
        var currentText = hyperlinkInput.val();

        // add the hide class name if doesn't enter any text
        if ($.isEmpty(currentText)) {
            hyperlinkWrapper.addClass('hide');
        
        } else {
            // if more than one character then only populate that field
            hyperlinkWrapper.removeClass('hide');
        };   
    });

    var dateInput = $('[data-field-class-input-date]');
    var dateWrapper = $('[data-field-type-date]');
    var datePicker = $('[data-date-picker]');
    var dateButton = $('[data-field-class-input-date-button]');

    dateButton.on('click', function(e) {

        // by default it will show the date immediately when you click on that button
        dateWrapper.removeClass('hide');
    });

    // date input field
    datePicker.on('change', function(e) {

        // check the current date value
        var currentDate = dateInput.val();

        // add the hide class name if doesn't enter any date
        if ($.isEmpty(currentDate)) {
            dateWrapper.addClass('hide');
        
        } else {
            dateWrapper.removeClass('hide');
        };   
    });

    var radioInput = $('[data-field-class-input-radio]');
    var radioWrapper = $('[data-field-type-radio]');

    // radio input field
    radioInput.on('click', function(e) {

        // check the current selected option
        var radioOptions = radioInput.val();

        // add the hide class name if doesn't select any option
        if ($.isEmpty(radioOptions)) {
            radioWrapper.addClass('hide');
        
        } else {
            // if more than one selected option then only populate that field
            radioWrapper.removeClass('hide');
        };   
    });

    var checkboxInput = $('[data-field-class-input-checkbox]');
    var checkboxWrapper = $('[data-field-type-checkbox]');

    // checkbox input field
    checkboxInput.on('click', function(e) {

        // check the current checked option
        var checkOptions = checkboxInput.is(':checked');

        // add the hide class name if doesn't checked any option
        if (!checkOptions) {
            checkboxWrapper.addClass('hide');
        
        } else {
            // if more than one checked option then only populate that field
            checkboxWrapper.removeClass('hide');
        };   
    });     
});