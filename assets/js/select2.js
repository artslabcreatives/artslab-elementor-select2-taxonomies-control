(function($){
   $(document).ready(function() {
      $('.select2').select2({
         width: 'resolve'
      });
   })
   $(document).on("change", ".select2", function() {
      $('.select2').select2({
         width: 'resolve'
      });
   })
})(jQuery);