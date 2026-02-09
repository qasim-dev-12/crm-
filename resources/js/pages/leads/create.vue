
<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h4>Create Lead</h4>
      <router-link to="/leads" class="btn btn-secondary">
        Back
      </router-link>
    </div>

    <!-- Form Card -->
    <div class="card">
      <div class="card-body">
        <form @submit.prevent="submit">

          <div class="row">

            <!-- Salutation -->
            <div class="col-md-2">
              <label>Salutation</label>
              <select v-model="form.salutation" class="form-control">
                <option value="">Select</option>
                <option>Mr</option>
                <option>Mrs</option>
                <option>Ms</option>
              </select>
            </div>

            <!-- Name -->
            <div class="col-md-4">
              <label>Name <span class="text-danger">*</span></label>
              <input
                type="text"
                v-model="form.name"
                class="form-control"
                required
              />
            </div>

            <!-- Service Type -->
            <div class="col-md-3">
              <label>Service Type <span class="text-danger">*</span></label>
              <select
                v-model="form.service_type_id"
                class="form-control"
                required
              >
                <option value="">Select Service</option>
                <option
                  v-for="service in serviceTypes"
                  :key="service.id"
                  :value="service.id"
                >
                  {{ service.name }}
                </option>
              </select>
            </div>

            <!-- Area -->
            <div class="col-md-3">
              <label>Area</label>
              <input
                type="text"
                v-model="form.area"
                class="form-control"
              />
            </div>
            <div class="form-group col-md-6">
  <label>{{ $t('Vehicle Number') }}</label>
  <input
    v-model="form.vehicle_number"
    type="text"
    class="form-control"
    placeholder="e.g. ABC-1234"
  />
</div>

            <!-- Charges -->
            <div class="col-md-3 mt-3">
              <label>Charges</label>
              <input
                type="number"
                v-model="form.charges"
                class="form-control"
              />
            </div>

            <!-- Mobile -->
            <div class="col-md-3 mt-3">
              <label>Mobile <span class="text-danger">*</span></label>
              <input
                type="text"
                v-model="form.mobile"
                class="form-control"
                required
              />
            </div>

            <!-- Status -->
            <div class="col-md-3 mt-3">
              <label>Status</label>
              <select v-model="form.status" class="form-control">
               <option value="New">New</option>
<option value="In Progress">In Progress</option>
<option value="Converted">Converted</option>
<option value="Cancelled">Cancelled</option>
              </select>
            </div>

          </div>

          <!-- Submit -->
          <div class="mt-4">
        <button
  type="submit"
  class="btn btn-primary"
  :disabled="loading"
>
  {{ loading ? 'Saving...' : 'Save Lead' }}
</button>
            <router-link to="/leads" class="btn btn-light ml-2">
              Cancel
            </router-link>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data () {
    return {
      loading: false,
      serviceTypes: [],
      errors: {},

      form: {
        salutation: '',
        name: '',
        service_type_id: '',
        area: '',
        charges: '',
        mobile: '',
        status: 'new',
         vehicle_number: '', // ✅
      },
    }
  },

  mounted () {
    this.fetchServiceTypes()
  },

  methods: {
    async fetchServiceTypes () {
      try {
        const res = await axios.get('/api/service-types')
        this.serviceTypes = res.data.data ?? res.data
      } catch (e) {
        this.safeToast('Failed to load service types', 'error')
      }
    },

    async submit () {
      if (this.loading) return
      this.loading = true
      this.errors = {}

      try {
        await axios.post('/api/leads', this.form)

        this.safeToast('Lead created successfully', 'success')

        // IMPORTANT: delay navigation for page transition
        setTimeout(() => {
          this.$router.push({ name: 'leads.index' })
        }, 400)

      } catch (err) {
        const message =
          err?.response?.data?.message || 'Validation failed'

        this.safeToast(message, 'error')
      } finally {
        this.loading = false
      }
    },

    // ✅ SAFE TOAST HELPER (THIS IS THE KEY)
    safeToast (message, type = 'success') {
      this.$nextTick(() => {
        if (this.$toast && typeof this.$toast[type] === 'function') {
          this.$toast[type](message)
        }
      })
    }
  }
}
</script>



