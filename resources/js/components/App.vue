<template>
  <div id="global">
    <loading ref="loading" />
    <transition name="page" mode="out-in">
      <component :is="layout" v-if="layout" />
    </transition>
    <div id="modal-here"></div>
  </div>
</template>

<script>
import Loading from "./Loading";

// Load layout components dynamically.
const requireContext = require.context("~/layouts", false, /.*\.vue$/);

const layouts = requireContext
  .keys()
  .map((file) => [file.replace(/(^.\/)|(\.vue$)/g, ""), requireContext(file)])
  .reduce((components, [name, component]) => {
    components[name] = component.default || component;
    return components;
  }, {});

export default {
  el: "#app",

  components: {
    Loading,
  },

  data: () => ({
    layout: null,
    defaultLayout: "default",
  }),

  metaInfo() {
    const { appName } = window.config;

    return {
      title: appName,
      titleTemplate: `%s · ${appName}`,
    };
  },

  mounted() {
    this.$loading = this.$refs.loading;
    this.getSettings();
    this.setupInactivityTimer();
  },

  methods: {
    // get settings
    async getSettings() {
      await this.$store.dispatch("operations/fetchSettingData");
    },
    setupInactivityTimer() {
      const timeoutMinutes = window.config.inactivity_timeout || 30;
      const timeoutMs = timeoutMinutes * 60 * 1000;
      let timer = null;
      const resetTimer = () => {
        if (timer) clearTimeout(timer);
        timer = setTimeout(async () => {
          if (this.$store.getters["auth/check"]) {
            await this.$store.dispatch("auth/logout");
            this.$router.push({ name: "login" });
          }
        }, timeoutMs);
      };
      ["mousemove", "keydown", "mousedown", "touchstart"].forEach(event => {
        window.addEventListener(event, resetTimer);
      });
      resetTimer();
    },

    /**
     * Set the application layout.
     *
     * @param {String} layout
     */
    setLayout(layout) {
      if (!layout || !layouts[layout]) {
        layout = this.defaultLayout;
      }

      this.layout = layouts[layout];
    },
  },
};
</script>
