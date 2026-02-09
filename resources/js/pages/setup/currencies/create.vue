<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-12 col-xl-3">
        <SettingsSidebar />
      </div>
      <div class="col-12 col-xl-9">
        <form role="form" @submit.prevent="saveCurrency" @keydown="form.onKeydown($event)">
          <div class="card">
            <div class="card-header setings-header">
              <div class="col-xl-4 col-4">
                <h3 class="card-title">
                  {{ $t("Create a currency") }}
                </h3>
              </div>
              <div class="col-xl-8 col-8 float-right text-right">
                <router-link :to="{ name: 'currencies.index' }" class="btn btn-dark float-right">
                  <i class="fas fa-long-arrow-alt-left" />
                  {{ $t("Back") }}
                </router-link>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="name">{{ $t("Name") }}<span class="required">*</span></label>
                <input id="name" v-model="form.name" type="text" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                  :placeholder="$t('Enter a name')" />
                <has-error :form="form" field="name" />
              </div>
              <div class="form-group">
                <label for="code">{{ $t("Code") }}<span class="required">*</span></label>
                <input id="code" v-model="form.code" type="text" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('code') }" name="code"
                  :placeholder="$t('Enter a code')" />
                <has-error :form="form" field="code" />
              </div>

              <div class="form-group">
                <label for="symbol">{{ $t("Symbol")
                }}<span class="required">*</span></label>
                <input id="symbol" v-model="form.symbol" type="text" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('symbol') }" name="symbol" :placeholder="$t('Enter a symbol')
                    " />
                <has-error :form="form" field="symbol" />
              </div>
              <div class="form-group">
                <label for="position">{{
                  $t("Currency Position")
                }}</label>
                <select v-model="form.position" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('position') }" name="position">
                  <option value="left">
                    {{
                      $t("Left align")
                    }}
                  </option>
                  <option value="right">
                    {{
                      $t("Right align")
                    }}
                  </option>
                </select>
                <has-error :form="form" field="position" />
              </div>

              <div class="form-group">
                <label for="decimal_places">{{ $t("Decimal Places") }}</label>
                <select
                  id="decimal_places"
                  v-model.number="form.decimal_places"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('decimal_places') }"
                  name="decimal_places"
                >
                  <option v-for="n in [0,2,3]" :key="n" :value="n">{{ n }}</option>
                </select>
                <has-error :form="form" field="decimal_places" />
              </div>

              <div class="form-group">
                <label for="format">{{ $t("Number Format") }}</label>
                <select
                  id="format"
                  v-model="form.format"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('format') }"
                  name="format"
                >
                  <option
                    v-for="f in formatOptions"
                    :key="f.value"
                    :value="f.value"
                  >
                    {{ f.label }}
                  </option>
                </select>
                <has-error :form="form" field="format" />
              </div>

              <div class="form-group">
                <label for="status">{{ $t("Status") }}</label>
                <select id="status" v-model="form.status" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('status') }">
                  <option value="1">{{ $t("Active") }}</option>
                  <option value="0">{{ $t("Inactive") }}</option>
                </select>
                <has-error :form="form" field="status" />
              </div>

              <div class="form-group">
                <label for="note">{{ $t("Note") }}</label>
                <textarea id="note" v-model="form.note" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('Write your note here!')" />
                <has-error :form="form" field="note" />
              </div>
            </div>

            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-save" /> {{ $t("Save") }}
              </v-button>
              <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                <i class="fas fa-power-off" /> {{ $t("Reset") }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Create Currency") };
  },
  data: () => ({
    breadcrumbsCurrent: "Create Currency",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Setup",
        url: "setup.index",
      },
      {
        name: "Currencies",
        url: "currencies.index",
      },
      {
        name: "Create",
        url: "",
      },
    ],
    form: new Form({
      name: "",
      note: "",
      status: 1,
      code: "",
      symbol: "",
      position: "left",
      decimal_places: 2,
      format: "en_US",
    }),
    loading: true,
  }),
  computed: {
    // Dropdown options with dynamic labels
    formatOptions() {
      const formats = [
        { value: "en_US", thousand: ",", decimal: "." },
        { value: "de_DE", thousand: ".", decimal: "," },
        { value: "fr_FR", thousand: " ", decimal: "," },
      ];

      return formats.map((f) => {
        return {
          value: f.value,
          label: this.formatExample(1234567.891, this.form.decimal_places, f.thousand, f.decimal),
        };
      });
    }
  },
  methods: {
    // Get separators based on format code
    getSeparators(format) {
      switch (format) {
        case "de_DE": return { thousand: ".", decimal: "," };
        case "fr_FR": return { thousand: " ", decimal: "," };
        default: return { thousand: ",", decimal: "." }; // en_US
      }
    },

    // Generic formatter
    formatExample(amount, decimals, thousandSep, decimalSep) {
      let fixed = amount.toFixed(decimals);
      let parts = fixed.split(".");
      parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSep);
      return parts.join(decimalSep);
    },

    // save currency
    async saveCurrency() {
      await this.form
        .post(window.location.origin + "/api/currencies")
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("Currency added successfully"),
          });
          this.$router.push({ name: "currencies.index" });
        })
        .catch(() => {
          toast.fire({
            type: "error",
            title: this.$t("Opps...something went wrong"),
          });
        });
    },
  },
};
</script>

