function customAlertError(message){
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: message,
    })
}

function customAlertSuccess(message){
    Swal.fire({
        position: 'center',
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 1500
    })
}