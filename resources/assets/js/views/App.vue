<template>
  <div id="smbr-main">
    <h1>SMB Randomizer</h1>

    <b-alert :show="error" variant="danger">
      Error: {{ this.error }}
    </b-alert>

    <b-alert :show="baseRomLoaded" variant="success">
      ROM file loaded OK!
    </b-alert>

    <smbr-rom-loader v-if="!baseRomLoaded" @update="updateRom" @error="onError"></smbr-rom-loader>

    <!--         <b-button class="mb-2" variant="primary">Primary</b-button>
        <b-button class="mb-2" variant="secondary">Secondary</b-button>
        <b-button class="mb-2" variant="success">Success</b-button>
        <b-button class="mb-2" variant="info">Info</b-button>
        <b-button class="mb-2" variant="warning">Warning</b-button>
        <b-button class="mb-2" variant="danger">Danger</b-button> -->
  </div>
</template>

<script>
export default {
  data() {
    return {
      baseRom: null,
      baseRomLoaded: false,
      baseRomHash: "",
      error: false
    };
  },

  created() {
    // Thanks Veetorp!
    localforage.getItem("rom").then(function(blob) {
      if (blob == null) {
        EventBus.$emit("noBlob");
        return;
      }
      EventBus.$emit("loadBlob", { target: { files: [new Blob([blob])] } });
    });
  },

  methods: {
    updateRom(rom, current_rom_hash) {
      if (!rom) {
        console.log(rom);
        return;
      }

      this.baseRom = rom;
      this.baseRomHash = current_rom_hash;
      this.baseRomLoaded = true;
      this.error = false;
    },
    onError(error) {
      this.error = error;
    }
  }
};
</script>