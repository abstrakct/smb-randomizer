<template>
    <div>
        <div v-if="!loading" id="rom-select">
            <b-card title="Select ROM File" style="max-width: 40rem;">
                <p class="card-text">
                    <p>
                        <b-form-file v-model="romFile" accept=".nes" @change="loadBlob" placeholder="Select ROM File..."></b-form-file>
                        <ol>
                            <li>Select your ROM file and load it into the browser. The randomizer will tell you if the selected ROM file is usable. The recommended ROM is named
                                <strong>Super Mario Bros. (JU) [!].nes</strong>
                            </li>
                            <li>Select which options to use for the randomization</li>
                            <li>Click Generate</li>
                            <li>Click Save</li>
                        </ol>
                    </p>
            </b-card>
        </div>
    </div>
</template>

<script>
export default {
  data() {
    return {
      loading: false,
      romFile: null,
      baseRomHash: "",
      baseRomSize: 0,
      loadedRomHash: "",
      settings_loaded: false
    };
  },

  created() {
    axios.get("/settings/base_rom").then(response => {
      this.baseRomHash = response.data.rom_hash;
      this.baseRomSize = response.data.rom_size;
      this.settings_loaded = true;
    });
  },

  mounted() {
    EventBus.$on("noBlob", this.noBlob);
    EventBus.$on("loadBlob", this.loadBlob);
  },

  methods: {
    noBlob() {
      this.loading = false;
    },

    loadBlob(change) {
      if (!this.settings_loaded) {
        return setTimeout(this.loadBlob, 50, change);
      }

      this.loading = true;
      let blob = change.target.files[0];

      new ROM(blob, rom => {
        this.loadedRomHash = rom.getMD5();
        if (this.loadedRomHash != this.baseRomHash) {
          this.$emit("error", "ROM File Not Recognized");
          this.loading = false;
          return;
        } else {
          localforage.getItem("romfilename").then(function(value) {
            if (value == null) {
              localforage.setItem("romfilename", change.target.files[0].name);
            }
          });

          localforage.setItem("rom", rom.getOriginalArrayBuffer());
          this.$emit("update", rom, this.baseRomHash);
          EventBus.$emit(
            "update-baserom-filename",
            change.target.files[0].name
          );
          this.loading = false;
        }
      });
    }
  }
};
</script>
