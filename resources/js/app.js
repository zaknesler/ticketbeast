import './bootstrap'

import ExampleComponent from './components/ExampleComponent.vue'
import PurchaseTickets from './components/PurchaseTickets.vue'

Vue.component('example-component', ExampleComponent)
Vue.component('purchase-tickets', PurchaseTickets)

const app = new Vue({
  el: '#app',
  data: {
    displayNavigation: false
  }
})
