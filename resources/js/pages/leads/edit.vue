<template>
  <div>
    <!-- Breadcrumbs -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $t('Edit Lead') }}</h3>

            <router-link
              :to="{ name: 'leads.index' }"
              class="btn btn-dark float-right"
            >
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>

          <!-- Form -->
          <form @submit.prevent="updateLead">
            <div class="card-body">
              <div class="row">
                <!-- Salutation -->
                <div class="form-group col-md-3">
                  <label>{{ $t('Salutation') }}</label>
                  <select v-model="form.salutation" class="form-control">
                    <option value="">Select</option>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                  </select>
                </div>

                <!-- Name -->
                <div class="form-group col-md-9">
                  <label>{{ $t('Name') }} *</label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="form-control"
                    required
                  />
                </div>
              </div>

              <div class="row">
                <!-- Service Type -->
                <div class="form-group col-md-6">
                  <label>{{ $t('Service Type') }} *</label>
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
                <div class="form-group col-md-6">
                  <label>{{ $t('Area') }}</label>
                  <input
                    v-model="form.area"
                    type="text"
                    class="form-control"
                  />
                </div>
              </div>

              <div class="row">
                <!-- Charges -->
                <div class="form-group col-md-6">
                  <label>{{ $t('Charges') }}</label>
                  <input
                    v-model="form.charges"
                    type="text"
                    class="form-control"
                  />
                </div>

                <div class="form-group col-md-6">
  <label>{{ $t('Vehicle Number') }}</label>
  <input
    v-model="form.vehicle_number"
    type="text"
    class="form-control"
  />
</div>

                <!-- Mobile -->
                <div class="form-group col-md-6">
                  <label>{{ $t('Mobile') }} *</label>
                  <input
                    v-model="form.mobile"
                    type="text"
                    class="form-control"
                    required
                  />
                </div>
              </div>

              <!-- Status -->
              <div class="form-group">
                <label>{{ $t('Status') }}</label>
                <select v-model="form.status" class="form-control">
                  <option value="New">New</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Converted">Converted</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
              </div>
            </div>

            <!-- Footer -->
            <div class="card-footer">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save" /> {{ $t('Update Lead') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'


export default {
  middleware: ['auth', 'check-permissions'],

  metaInfo() {
    return { title: this.$t('Edit Lead') }
  },

  data() {
    return {
      breadcrumbsCurrent: 'Edit Lead',
      breadcrumbs: [
        { name: 'Dashboard', url: 'home' },
        { name: 'Leads', url: 'leads.index' },
        { name: 'Edit', url: '' },
      ],

      form: {
        salutation: '',
        name: '',
        service_type_id: '',
        area: '',
        charges: '',
        mobile: '',
        status: 'New',
      },

      serviceTypes: [],
    }
  },

  mounted() {
    this.fetchLead()
    this.fetchServiceTypes()
  },

  methods: {
    async fetchLead() {
      const { data } = await axios.get(
        `/api/leads/${this.$route.params.slug}`
      )

      this.form = {
        salutation: data.data.salutation,
        name: data.data.name,
        service_type_id: data.data.service_type_id,
        area: data.data.area,
        charges: data.data.charges,
        mobile: data.data.mobile,
        status: data.data.status,
         vehicle_number: data.data.vehicle_number, // ✅
      }
    },

    async fetchServiceTypes() {
      const { data } = await axios.get('/api/service-types')
      this.serviceTypes = data.data ?? data
    },

    async updateLead() {
      await axios.put(
        `/api/leads/${this.$route.params.slug}`,
        this.form
      )

   this.$toast.success('Lead updated successfully')
      this.$router.push({ name: 'leads.index' })
    },
  },
}
</script>
