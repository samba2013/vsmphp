<script src="<?php echo APP_HOST ?>/assets/lib/jquery/jquery-3.5.1.js"></script>
<script src="<?php echo APP_HOST ?>/assets/lib/jquery/popper.min.js"></script>
<script src="<?php echo APP_HOST ?>/assets/lib/bootstrap/js/bootstrap.js"></script>
<script src="<?php echo APP_HOST ?>/assets/lib/fontawesome/js/all.js" type="application/javascript" ></script>
<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
