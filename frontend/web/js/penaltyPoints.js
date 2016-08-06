$(document).ready(function () {
    $(function () {
        $('#div-circle').hide();
        $('#div-device').hide();
        if ($('#penaltypoints-scenario').val() == 'CIRCLE')
        {
            $('#div-circle').show('slow');
            $('#div-device').hide('slow');
            $('#penaltypoints-circle').val($('#penaltypoints-circleval').val());
        }
        if ($('#penaltypoints-scenario').val() == 'DEVICE')
        {
            $('#div-circle').hide('slow');
            $('#div-device').show('slow');
            $('#penaltypoints-device').val($('#penaltypoints-deviceval').val());
        }
        $('#penaltypoints-scenario').change(function () {
            if ($('#penaltypoints-scenario').val() == 'PAN-INDIA')
            {
                $('#div-circle').hide('slow');
                $('#div-device').hide('slow');
                $('#penaltypoints-circle').val('');
                $('#penaltypoints-device').val('');
            }
            else if ($('#penaltypoints-scenario').val() == 'CIRCLE')
            {
                $('#div-circle').show('slow');
                $('#div-device').hide('slow');
                $('#penaltypoints-circle').val($('#penaltypoints-circleval').val());
                $('#penaltypoints-device').val('');
            }
            else if ($('#penaltypoints-scenario').val() == 'DEVICE')
            {
                $('#div-circle').hide('slow');
                $('#div-device').show('slow');
                $('#penaltypoints-circle').val('');
                $('#penaltypoints-device').val($('#penaltypoints-deviceval').val());
            }
        })
    });
})

