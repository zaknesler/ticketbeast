<template>
  <div>
    <form class="flex justify-between items-center" @submit.prevent="openStripe">
      <div class="mr-3 flex items-center flex-grow">
        <select
          v-if="maxTickets"
          class="form-select py-3 block w-full h-full"
          v-model="form.quantity"
          :class="{ 'opacity-50 pointer-events-none select-none': processing }"
        >
          <option
            v-for="i in maxTickets"
            :value="i"
            :key="i"
            v-text="i"
            :selected="i == form.quantity"
          ></option>
        </select>

        <div v-else>
          <div class="font-semibold text-gray-800">Sold out!</div>
          <div class="mt-1 text-sm leading-tight text-gray-600">Check back later for openings!</div>
        </div>
      </div>

      <div
        class="ml-3 block"
        :class="{ 'cursor-not-allowed': maxTickets === 0 || processing }"
      >
        <button
          class="btn"
          :disabled="maxTickets === 0"
          :class="{ 'opacity-50 pointer-events-none select-none': maxTickets === 0 || processing }"
        >Buy Tickets</button>
      </div>
    </form>
  </div>
</template>

<script>
  export default {
    props: {
      maxTickets: Number,
      concertId: Number,
      concertTitle: String,
      ticketPrice: Number,
    },

    data: () => ({
      form: {
        quantity: 1,
      },
      processing: false,
      stripeHandler: null,
    }),

    computed: {
      description() {
        if (this.form.quantity > 1) {
          return `${this.form.quantity} tickets to ${this.concertTitle}`
        }

        return `One ticket to ${this.concertTitle}`
      },
      totalPrice() {
        return this.form.quantity * this.ticketPrice
      },
      priceInDollars() {
        return (this.ticketPrice / 100).toFixed(2)
      },
      totalPriceInDollars() {
        return (this.totalPrice / 100).toFixed(2)
      },
    },

    methods: {
      openStripe(callback) {
        this.stripeHandler.open({
          name: 'Ticketbeast',
          description: this.description,
          currency: "usd",
          allowRememberMe: false,
          panelLabel: 'Pay {{amount}}',
          amount: this.totalPrice,
          image: 'https://placehold.it/100x100',
          token: this.purchase,
        })
      },

      purchase(token) {
        this.processing = true

        axios.post(`/concerts/${this.concertId}/orders`, {
          email: token.email,
          ticket_quantity: this.form.quantity,
          payment_token: token.id,
        }).then(({ data }) => {
          window.location.replace(`/orders/${data.confirmation_number}`)
        }).catch(response => {
          this.processing = false
        })
      }
    },

    created() {
      this.stripeHandler = StripeCheckout.configure({
        key: process.env.MIX_STRIPE_KEY,
      })

      window.addEventListener('popstate', () => {
        handler.close()
      })
    }
  }
</script>
