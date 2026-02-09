<template>
  <div class="card">
    <div class="card-header">
      <h3>Lead Details</h3>
    </div>

    <div class="card-body" v-if="lead">
      <p><strong>Salutation:</strong> {{ lead.salutation }}</p>
      <p><strong>Name:</strong> {{ lead.name }}</p>
      <p><strong>Service Type:</strong> {{ lead.service_type?.name }}</p>
      <p><strong>Area:</strong> {{ lead.area }}</p>
      <p><strong>Charges:</strong> {{ lead.charges }}</p>
      <p><strong>Mobile:</strong> {{ lead.mobile }}</p>
      <p>
        <strong>Status:</strong>
        <span class="badge bg-success">{{ lead.status }}</span>
      </p>

      <router-link
        class="btn btn-secondary mt-3"
        :to="{ name: 'leads.index' }"
      >
        Back
      </router-link>
    </div>

    <div v-else class="text-center p-4">
      Loading...
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      lead: null,
    }
  },

  async created() {
    await this.fetchLead()
  },

  methods: {
    async fetchLead() {
      try {
        const res = await axios.get(
          `/api/leads/${this.$route.params.slug}`
        )
        this.lead = res.data.data
      } catch (error) {
        this.$toast.error('Failed to load lead')
      }
    },
  },
}
</script>
