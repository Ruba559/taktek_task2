<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taktek</title>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
 

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }


        .grey-bg {
            background-color: #777777;
        }

        .blue-bg {
            background-color: #41A5F3;
        }

        .green-bg {
            background-color: #12D1B7;
        }

        .warning-bg {
            background-color: #f8aa44 !important;
        }

        .red-text {
            color: red;
        }
    </style>
       <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
       <script src="/livewire/livewire.js"></script>
    @livewireStyles
   
   
</head>

<body>


    <div class="container-fluid" style="margin-top: 100px">

      {{ $slot }}
    </div>

    @livewireScripts



    <script>
        window.addEventListener('close-modal', event => {

            $('#addmodal').modal('hide');

        });
        $('#addmodal').on('hidden.bs.modal', function() {

            Livewire.dispatch('closeModal')
        });
    </script>
    <script>
        var progressbar = $('.progress-bar');

        document.addEventListener('livewire-upload-finish', () => {
            progressbar.hide();
        });
        document.addEventListener('livewire-upload-error', () => {
            progressbar.addClass('bg-danger');
        });
        document.addEventListener('livewire-upload-progress', (event) => {
            progressbar.css('width', event.detail.progress + "%");
            if (event.detail.progress > 30 && event.detail.progress < 50) {
                progressbar.addClass('blue-bg');
            } else if (event.detail.progress > 50) {
                progressbar.removeClass('blue-bg');
                progressbar.addClass('green-bg');
            }
            progressbar.text(event.detail.progress + "%");
        });
    </script>


</body>

</html>
