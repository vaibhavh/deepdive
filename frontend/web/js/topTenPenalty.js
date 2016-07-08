$(document).ready(function () {
    $(function () {
        $('#div-circle').hide();
        $('#div-device').hide();
        if ($('#penaltytopten-scenario').val() == 'CIRCLE')
        {
            $('#div-circle').show('slow');
            $('#div-device').hide('slow');
        }
        if ($('#penaltytopten-scenario').val() == 'DEVICE')
        {
            $('#div-circle').hide('slow');
            $('#div-device').show('slow');
        }
        $('#penaltytopten-scenario').change(function () {
            if ($('#penaltytopten-scenario').val() == 'PAN-INDIA')
            {
                $('#div-circle').hide('slow');
                $('#div-device').hide('slow');
            }
            else if ($('#penaltytopten-scenario').val() == 'CIRCLE')
            {
                $('#div-circle').show('slow');
                $('#div-device').hide('slow');
            }
            else if ($('#penaltytopten-scenario').val() == 'DEVICE')
            {
                $('#div-circle').hide('slow');
                $('#div-device').show('slow');
            }

        })
    });
})

