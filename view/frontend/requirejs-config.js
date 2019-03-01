var config = {
   map: {
      '*': {
         "Magento_Checkout/js/model/shipping-save-processor/default" : 'Mageinn_EditOrder/js/model/shipping-save-processor/default'
      }
   },
   config: {
      mixins: {
         'Magento_Checkout/js/view/payment/default': {
            'Mageinn_EditOrder/js/view/payment/default-mixin': true
         }
      }
   }
};