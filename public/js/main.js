$(document).ready(function () {
  window._token = $('meta[name="csrf-token"]').attr('content')

  moment.updateLocale('en', {
    week: {dow: 1} // Monday is the first day of the week
  })

  //Date range picker
  $('.date-range').daterangepicker({
    format: 'DD-MM-YYYY',
    locale: 'en',
  })

  $('.date').datetimepicker({
    format: 'DD-MM-YYYY',
    locale: 'en',
    icons: {
      up: 'fas fa-chevron-up',
      down: 'fas fa-chevron-down',
      previous: 'fas fa-chevron-left',
      next: 'fas fa-chevron-right'
    }
  })

  $('.datetime').datetimepicker({
    format: 'DD-MM-YYYY HH:mm:ss',
    locale: 'en',
    sideBySide: true,
    icons: {
      up: 'fas fa-chevron-up',
      down: 'fas fa-chevron-down',
      previous: 'fas fa-chevron-left',
      next: 'fas fa-chevron-right'
    }
  })

  $('.timepicker').datetimepicker({
    format: 'HH:mm:ss',
    icons: {
      up: 'fas fa-chevron-up',
      down: 'fas fa-chevron-down',
      previous: 'fas fa-chevron-left',
      next: 'fas fa-chevron-right'
    }
  })

  $('.select-all').click(function () {
    let $select2 = $(this).parent().siblings('.select2')
    $select2.find('option').prop('selected', 'selected')
    $select2.trigger('change')
  })
  $('.deselect-all').click(function () {
    let $select2 = $(this).parent().siblings('.select2')
    $select2.find('option').prop('selected', '')
    $select2.trigger('change')
  })

  $('.select2').select2()

  $('.treeview').each(function () {
    var shouldExpand = false
    $(this).find('li').each(function () {
      if ($(this).hasClass('active')) {
        shouldExpand = true
      }
    })
    if (shouldExpand) {
      $(this).addClass('active')
    }
  })

  $('a[data-widget^="pushmenu"]').click(function () {
    setTimeout(function() {
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    }, 350);
  })
})

if (window.numeral) {
    window.numeral.register('locale', 'id', {
      delimiters: {
        thousands: '.',
        decimal: ','
      },
      abbreviations: {
        thousand: 'rb',
        million: 'jt',
        billion: 'm',
        trillion: 't'
      },
      ordinal: numeral.locales.en.ordinal,
      currency: {
        symbol: 'Rp'
      }
    });

    window.numeral.locale('id');
  };

$(".bootstrap-switch").each(function(){
    $(this).bootstrapSwitch('state', $(this).prop('checked'));
})

function convertToRupiah(angka)
{
    var rupiah = '';
    var angkarev = angka.toString().split('').reverse().join('');
    for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
    return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('');
}
