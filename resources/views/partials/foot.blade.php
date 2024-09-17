


  <script src="<?= asset("assets/js/bootstrap.bundle.min.js") ?>"></script>
  <script src="<?= asset("assets/js/jquery.min.js") ?>"></script>
  <script src="<?= asset("assets/js/summernote.min.js") ?>"></script>
  <script src="<?= asset("assets/js/toastr.min.js") ?>"></script>
  <script src="<?= asset("assets/js/sweetalert2.min.js") ?>"></script>
  <script>
    $(function(){
          $(document).on('click', '.delete-btn', function(e){
                e.preventDefault();
                if(confirm('Are you sure?'))
                {
                  $(this).closest('form#delete-form').submit();
                }
            });        
            
            $(document).on('change', '#image', function(e){
                let file = e.target.files[0];
                let tempURL = URL.createObjectURL(file);
                $(document).find('div.preview-img').remove();
                $(this).after(`<div class="preview-img mt-2">
                  <img src="${tempURL}" class="h-50 w-50"/>
                </div>`);
            });

            $('.summernote').summernote({
              height: 200,
            });

            setTimeout(() => {
              remove_alert();
            }, 5000);

            $(document).on('click', 'button.btn-close', function(e){
                remove_alert();
            });

            function remove_alert()
            {
                <?php destroy('message'); ?>
                $(document).find('div.alert').remove();
            }

          const loading_spinner = '<i class="fa fa-spinner fa-spin"></i><span style="margin-left:5px !important;">Please wait...</span>';
          $('#summernote').summernote({
              placeholder: 'Description...',
              tabsize: 2,
              height: 100
          });
          function loading(element, status = true, btnText = 'Submit')
          {

              let submit_element = element.find('button[type="submit"], input[type="submit"]');
              let _attr = submit_element.attr('title');
              btnText = (typeof _attr != "undefined" && _attr != '') ? _attr : btnText;
              if(status)
              {
                  submit_element.attr('disabled', true);
                  submit_element.html(loading_spinner);
              }
              else{
                  submit_element.attr('disabled', false);
                  submit_element.html(btnText);
              }

          }
          //For submit a form
          $(document).on('submit', 'form#ajaxForm', function(e){
              e.preventDefault();
              toastr.clear();
              $(document).find('span.error').remove();
              $('input, select, textarea').removeClass('is-invalid');
              let url = $(this).attr('action');
              let method = $(this).attr('method');
              let data = new FormData(this);
              let form = $(this);
              $.ajax({
                  url,
                  method,
                  data,
                  cache: false,
                  contentType: false,
                  processData: false,
                  beforeSend: function()
                  {
                      loading(form);
                  },
                  success:function(res)
                  {
                      res = JSON.parse(res);
                      loading(form, false);
                      if(res.success){
                          form[0].reset();  
                          $(document).find('.modal').modal('hide');
                          toastr.success(res.message);
                          if(res.url)
                          {
                              setTimeout(() => {
                                document.location.href = res.url;
                              }, 500);
                          }
                      }
                      else{
                          $.each(res.errors, function(name, msg){
                            let elements = $(document).find(`[name="${name}"]`);
                            if (elements.length > 0)
                            {
                                elements.addClass('is-invalid')
                                        .after(`<span class="text-danger error">${msg}</span>`)
                            } else
                            {
                                toastr.error(msg);
                            }

                        });
                      }
                  },
                  error:function(res)
                  {
                      loading(form, false);
                      $.each(res.responseJSON.errors, function(name, msg){
                          let elements = $(document).find(`[name="${name}"]`);
                          if (elements.length > 0)
                          {
                              elements.addClass('is-invalid')
                                      .after(`<span class="text-danger error">${msg}</span>`)
                          } else
                          {
                              toastr.error(msg);
                          }

                      });
                  }
              });
        });

          $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const type = "DELETE";
            Swal.fire({
                title: 'Are you sure ?',
                text: `This will be removed after confirmation`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).prop('disabled', true);
                    $.ajax({
                        url,
                        type,
                        success: function (res) {
                            res = JSON.parse(res);
                            if (res.success) {
                                toastr.success(res.message);
                                $(this).prop('disabled', false);
                                if(res.url)
                                {
                                    setTimeout(() => {
                                      document.location.href = res.url;
                                    }, 500);
                                }
                            } else {
                                toastr.error(res.message);
                                $(this).prop('disabled', false);
                            }

                        },
                        error: function (xhr, exception) {
                            var msg = "";
                            if (xhr.status === 0) {
                                msg = "Not connect.\n Verify Network." + xhr.responseText;
                            } else if (xhr.status == 404) {
                                msg = "Requested page not found. [404]" + xhr.responseText;
                            } else if (xhr.status == 500) {
                                msg = "Internal Server Error [500]." + xhr.responseText;
                            } else if (exception === "parsererror") {
                                msg = "Requested JSON parse failed.";
                            } else if (exception === "timeout") {
                                msg = "Time out error." + xhr.responseText;
                            } else if (exception === "abort") {
                                msg = "Ajax request aborted.";
                            } else {
                                msg = "Error:" + xhr.status + " " + xhr.responseText;
                            }
                            Swal.fire(msg)
                        }
                    });
                }
          });
        });
        
    });
</script>
</body>
</html>
