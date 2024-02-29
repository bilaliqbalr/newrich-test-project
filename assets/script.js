function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function validateForm(form) {
    // validate form required and email fields
    var isValid = true;
    form.find('.alert').remove();
    var errors = {};
    form.find('input, textarea').each(function() {
        var input = $(this);
        if (input.attr('required') && !input.val()) {
            errors[input.attr('name')] = input.attr('name') + ' is required';
            isValid = false;
        }
        if (input.attr('type') === 'email' && input.val() && !validateEmail(input.val())) {
            errors[input.attr('name')] = input.val() + ' is not a valid email';
            isValid = false;
        }
    });

    if (!isValid) {
        printErrors(form, errors, true);
    }

    return isValid;
}

function printErrors(form, errors, withData = false) {
    for (var key in errors) {
        key = withData ? key : 'data[' + key + ']';
        form.find('[name="' + key + '"]').after('<div class="small text-danger">' + errors[key] + '</div>');
    }
}

jQuery(document).ready(function($) {
    $('form').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('[type="submit"]');

        // checking if button is disabled then return to prevent unwanted requests
        if (btn.attr('disabled')) {
            return;
        }

        // validate form
        if (!validateForm($(this))) {
            return;
        }

        // disabled submit button & clear previous errors
        btn.attr('disabled', true);
        form.find('.text-danger').remove();

        var data = form.serialize();
        var url = form.attr('action');
        var method = form.attr('method');
        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function(response) {
                if (response.status) {
                    form.html('<div class="alert alert-success">Form submitted successfully</div>');
                } else {
                    form.find('.alert').remove();
                    printErrors(form, response.errors);
                }

                // enable submit button
                btn.attr('disabled', false);
            },
            error: function() {
                form.prepend('<div class="alert alert-danger">An error occurred</div>');

                // enable submit button
                btn.attr('disabled', false);
            }
        });
    });
});

// load submission details
document.getElementById('submissionModal').addEventListener('show.bs.modal', function(event) {
    var submission = submissions[jQuery(event.relatedTarget).data('id')];
    var data = submission['data'];
    var cont = $('#submission-details');
    cont.html('');
    for (var key in data) {
        cont.append('<div class="row mb-2"><div class="col-4"><strong>' + key + '</strong></div><div class="col-8">' + data[key] + '</div></div>');
    }
});
