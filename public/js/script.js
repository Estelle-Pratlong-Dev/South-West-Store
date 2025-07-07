$(document).ready(function () {

    $(".add-product").click(function () {

        Swal.fire({
            position: 'center',
            type: 'success',
            title: 'Article ajouté',
            showConfirmButton: false,
            timer: 1500
        });
    });


});