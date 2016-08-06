$(document).ready(function () {
    $(function () {
        $('#div-circle').hide();
        $('#div-device').hide();
        if ($('#penaltytopten-scenario').val() == 'CIRCLE')
        {
            $('#div-circle').show('slow');
            $('#div-device').hide('slow');
            $('#penaltytopten-circle').val($('#penaltytopten-circleval').val());
        }
        if ($('#penaltytopten-scenario').val() == 'DEVICE')
        {
            $('#div-circle').hide('slow');
            $('#div-device').show('slow');
            $('#penaltytopten-device').val($('#penaltytopten-deviceval').val());
        }
        $('#penaltytopten-scenario').change(function () {
            if ($('#penaltytopten-scenario').val() == 'PAN-INDIA')
            {
                $('#div-circle').hide('slow');
                $('#div-device').hide('slow');
                $('#penaltytopten-circle').val('');
                $('#penaltytopten-device').val('');
                
            }
            else if ($('#penaltytopten-scenario').val() == 'CIRCLE')
            {
                $('#div-circle').show('slow');
                $('#div-device').hide('slow');
                $('#penaltytopten-circle').val($('#penaltytopten-circleval').val());
                $('#penaltytopten-device').val('');
            }
            else if ($('#penaltytopten-scenario').val() == 'DEVICE')
            {
                $('#div-circle').hide('slow');
                $('#div-device').show('slow');
                $('#penaltytopten-circle').val('');
                $('#penaltytopten-device').val($('#penaltytopten-deviceval').val());
            }
        })
    });
})

