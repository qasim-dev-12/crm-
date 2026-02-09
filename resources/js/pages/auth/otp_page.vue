<template>
  <div class="container-fluid">
    <div class="row no-gutter">
      <!-- The image half -->
      <div class="col-md-6 d-none d-md-flex bg-image"></div>
      <!-- The content half -->
      <div class="col-md-6 bg-light">
        <div class="auth-wrapper d-flex align-items-center py-5">
          <!-- Demo content-->
          <div class="container">
            <div class="row">
              <div class="col-lg-10 col-xl-7 mx-auto">
                <p class="text-muted mb-4 mt-2">
                  {{ $t("Otp sent to your email. Please check your email!") }}
                </p>
                <form
                  @submit.prevent="verifyOTP"
                  @keydown="form.onKeydown($event)"
                >
                  <div class="form-group mb-3">
                    <label for="otp_number"
                      >{{ $t("Otp Number") }}
                      <span class="required">*</span></label
                    >
                    <input
                      v-model="form.otp_number"
                      :class="{ 'is-invalid': form.errors.has('otp_number') }"
                      class="form-control rounded-pill border-0 shadow-sm px-4 text-primary"
                      type="number"
                      name="otp_number"
                      :placeholder="$t('Enter valid OTP number')"
                    />
                    <div v-if="errorMessage" class="text-danger">
                      {{ errorMessage }}
                    </div>
                  </div>

                  <!-- Countdown Timer Display -->
                  <div class="text-danger mb-3">
                    <strong v-if="!isExpired">{{ formattedTime }}</strong>
                    <strong v-else class="text-danger">
                      {{ $t("OTP expired. Please resend code.") }}
                    </strong>
                  </div>

                  <!-- Submit Button (Verify) -->
                  <v-button
                    :loading="verifyLoading"
                    :disabled="isExpired"
                    class="btn btn-primary btn-block text-uppercase mb-2 rounded-pill shadow-sm"
                  >
                    <i class="fas fa-sign-in-alt"></i>
                    <strong>{{ $t("Verify") }}</strong>
                  </v-button>
                </form>

                <!-- Resend Code Button (Resend) -->
                <button
                  v-if="isExpired"
                  @click="resendOTP"
                  :disabled="resendLoading"
                  class="btn btn-secondary btn-block text-uppercase mb-2 rounded-pill shadow-sm"
                >
                  <i v-if="!resendLoading" class="fas fa-redo"></i>
                  <i v-if="resendLoading" class="fas fa-spinner fa-spin"></i>
                  <strong>{{ $t("Resend Code") }}</strong>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- End -->
    </div>
  </div>
</template>

<script>
import Form from "vform";
import Cookies from "js-cookie";
import { mapGetters } from "vuex";
import Button from "../../components/Button.vue";

export default {
  components: { Button },
  layout: "basic",
  middleware: "guest",
  metaInfo() {
    return { title: this.$t("Otp Verification") };
  },
  data() {
    return {
      form: new Form({
        otp_number: "",
        token_number: localStorage.getItem("otp_token"),
      }),
      timer: 120, // Default 2 minutes in seconds
      interval: null,
      isExpired: false,
      errorMessage: "", // Variable to store error message
      verifyLoading: false, // Loading flag for verify button
      resendLoading: false, // Loading flag for resend button
    };
  },
  computed: {
    // Format the remaining time as MM:SS
    formattedTime() {
      const minutes = Math.floor(this.timer / 60);
      const seconds = this.timer % 60;
      return `${minutes.toString().padStart(2, "0")}:${seconds
        .toString()
        .padStart(2, "0")}`;
    },
  },
  methods: {
    async verifyOTP() {
      if (this.isExpired) {
        alert("OTP has expired. Please request a new OTP.");
        return;
      }

      this.verifyLoading = true; // Show loading on Verify button

      try {
        const { data } = await this.form.post("/api/verify-otp");

        if (data.token) {
          localStorage.removeItem("otp_timer");
          await this.$store.dispatch("auth/saveToken", {
            token: data.token,
            remember: this.remember,
          });

          await this.$store.dispatch("auth/fetchUser");

          this.redirect();
        } else {
          this.errorMessage = data.message || "Verification failed.";
        }
      } catch (error) {
        let message = "OTP verification failed.";
        if (error.response && error.response.data) {
          if (error.response.data.message) {
            message = error.response.data.message;
          }
          if (error.response.data.errors) {
            this.form.errors.set(error.response.data.errors);
          }
        }
        this.errorMessage = message;
      } finally {
        this.verifyLoading = false; // Hide loading after completion
      }
    },

    redirect() {
      const intendedUrl = Cookies.get("intended_url");
      if (intendedUrl) {
        Cookies.remove("intended_url");
        this.$router.push({ path: intendedUrl });
      } else {
        this.$router.push({ name: "home" });
      }
    },

    startTimer() {
      this.interval = setInterval(() => {
        if (this.timer > 0) {
          this.timer--;
        } else {
          this.isExpired = true;
          clearInterval(this.interval);
        }
      }, 1000); // Countdown by 1 second
    },

    async resendOTP() {
      this.resendLoading = true; // Show loading on Resend button

      try {
        const { data } = await this.form.post("/api/resend-otp");

        if (data.status === "otp_resent") {
          // Get OTP expiration time from settings (default to 2 minutes)
          const otpExpirationTime = data.otp_expiration_time || 2;
          this.timer = otpExpirationTime * 60; // Convert minutes to seconds
          this.isExpired = false; // Clear the expired flag

          localStorage.setItem("otp_timer", Date.now()); // Save the new timer start time
          clearInterval(this.interval); // Clear the previous interval
          this.startTimer(); // Start a new countdown

          this.errorMessage =
            data.message || "OTP has been resent. Please check your email.";
        } else {
          this.errorMessage = "Something went wrong.";
        }
      } catch (error) {
        this.errorMessage = "Failed to resend OTP. Please try again later.";
      } finally {
        this.resendLoading = false; // Hide loading after completion
      }
    },
  },
  mounted() {
    const savedTime = localStorage.getItem("otp_timer");
    const savedExpirationTime = localStorage.getItem("otp_expiration_time");

    if (savedTime) {
      const timeElapsed = Math.floor((Date.now() - savedTime) / 1000);
      const expirationTimeInSeconds = (savedExpirationTime || 2) * 60;
      this.timer = Math.max(expirationTimeInSeconds - timeElapsed, 0);

      if (this.timer === 0) {
        this.isExpired = true;
      } else {
        this.startTimer();
      }
    } else {
      // Get OTP expiration time from localStorage (set during login)
      const otpExpirationTime = localStorage.getItem("otp_expiration_time") || 2;
      this.timer = otpExpirationTime * 60; // Convert minutes to seconds
      localStorage.setItem("otp_timer", Date.now());
      localStorage.setItem("otp_expiration_time", otpExpirationTime);
      this.startTimer();
    }
  },
  beforeDestroy() {
    clearInterval(this.interval);
  },
};
</script>
