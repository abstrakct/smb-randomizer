<template>
  <div id="smbr-main">
    <b-container fluid>
      <b-row>
        <b-col></b-col>
        <b-col cols="8">
          <b-card :title="'Super Mario Bros. Randomizer v' + this.version" style="max-width: 150rem;">

            <b-alert dismissible fade :show="error" variant="danger">
              Error: {{ this.errorMessage }}
            </b-alert>

            <b-row>
              <b-col>
              </b-col>
              <b-col>
              </b-col>
            </b-row>
            <smbr-rom-loader v-if="!baseRomLoaded" @update="updateRom" @error="onError"></smbr-rom-loader>

            <p> </p>

            <div v-if="baseRomLoaded && optionsLoaded">
              <b-card title="Options" style="max-width: 150rem;">
                <b-row>
                  <b-col>
                    <b-button @click="loadDefaults" variant="info">Load default options</b-button>
                  </b-col>
                  <b-col>
                  </b-col>
                  <b-col>
                    <b-form-input v-model="selectedOptions.seed" id="inputseed" type="number" placeholder="Input seed number (leave blank for random)"></b-form-input>
                  </b-col>
                </b-row>
                <p></p>
                <b-row>
                  <b-col>
                    <!--                     <b-form-group label="Level Randomization" label-for="olr">
                      <b-select id="olr" @input="updateInputted" v-model="selectedOptions.shuffleLevels" :options="randomizerOptions.shuffleLevels">
                      </b-select>
                    </b-form-group> -->

                    <smbr-select id="osl" label="Level Randomization" @input="updateInputted" storage-key="smbr.opt.levels" v-model="selectedOptions.shuffleLevels" :selected="selectedOptions.shuffleLevels" :options="randomizerOptions.shuffleLevels"></smbr-select>

                    <b-form-group>
                      <b-form-checkbox id="owl" @input="updateInputted" v-model="selectedOptions.normalWorldLength" value="false" unchecked-value="true">Worlds can have varying length</b-form-checkbox>
                      <b-form-checkbox id="opt" @input="updateInputted" v-model="selectedOptions.pipeTransitions" value="remove" unchecked-value="keep">Remove pipe transitions</b-form-checkbox>
                    </b-form-group>
                    <b-form-group label="Warp Zones" label-for="owz">
                      <b-select id="owz" @input="updateInputted" v-model="selectedOptions.warpZones" :options="randomizerOptions.warpZones">
                      </b-select>
                    </b-form-group>
                    <b-form-group>
                      <b-form-checkbox id="ohw" @input="updateInputted" v-model="selectedOptions.hiddenWarpDestinations" value="true" unchecked-value="false">Hide Warp Pipe destination worlds</b-form-checkbox>
                    </b-form-group>
                    <b-form-group label="Blocks" label-for="obl">
                      <b-select id="obl" @input="updateInputted" v-model="selectedOptions.blocks" :options="randomizerOptions.blocks">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Enemies" label-for="oen">
                      <b-select id="oen" @input="updateInputted" v-model="selectedOptions.enemies" :options="randomizerOptions.enemies">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Bowser's Hitpoints" label-for="obh">
                      <b-select id="obh" @input="updateInputted" v-model="selectedOptions.bowserHitpoints" :options="randomizerOptions.bowserHitpoints">
                      </b-select>
                    </b-form-group>
                    <b-form-group>
                      <b-form-checkbox id="oba" @input="updateInputted" v-model="selectedOptions.bowserAbilities" value="true" unchecked-value="false">Randomize which worlds Bowser starts throwing hammers and breathing fire</b-form-checkbox>
                    </b-form-group>
                  </b-col>
                  <b-col>
                    <b-form-group label="Mario Color Scheme" label-for="ocsm">
                      <b-select id="ocsm" @input="updateInputted" v-model="selectedOptions.colorscheme.mario" :options="randomizerOptions.colorscheme.mario">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Luigi Color Scheme" label-for="ocsl">
                      <b-select id="ocsl" @input="updateInputted" v-model="selectedOptions.colorscheme.luigi" :options="randomizerOptions.colorscheme.luigi">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Fire Mario/Luigi Color Scheme" label-for="ocsf">
                      <b-select id="ocsf" @input="updateInputted" v-model="selectedOptions.colorscheme.fire" :options="randomizerOptions.colorscheme.fire">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Starting Lives" label-for="osl">
                      <b-select id="osl" @input="updateInputted" v-model="selectedOptions.startingLives" :options="randomizerOptions.startingLives">
                      </b-select>
                    </b-form-group>
                    <p> </p>

                    <b-button variant="success" @click="generateSeed" class="w-100">Generate!</b-button>
                    <div v-if="rando.stored">
                      <p></p>
                      <b-button variant="success" @click="saveRandomizedRom" class="w-100">Save
                        <strong>{{ rando.filename }}</strong>
                      </b-button>
                      <p></p>
                      <b-button variant="info" class="w-100" :href="rando.logfullpath">View log (contains spoilers!)</b-button>
                    </div>
                  </b-col>
                </b-row>
              </b-card>
            </div>

            <p class="card-text">

            </p>
          </b-card>
        </b-col>
        <b-col>
          <b-alert :show="baseRomLoaded" variant="success" class="m-3">
            <center>
              <h6 class="alert-heading">ROM file loaded OK!</h6>
              <b-button @click="unloadRom">Unload ROM file</b-button>
            </center>
          </b-alert>
          <b-card title="Information">
            <b-alert dismissible :show="info" variant="info" v-html="infoMessage">
              {{ infoMessage }}
            </b-alert>
          </b-card>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
export default {
  props: ["version"],

  // TODO: Store selected options in localNorage
  // TODO: Disable certain options when certain options are selected!
  data() {
    return {
      // base rom
      baseRom: null,
      baseRomLoaded: false,
      baseRomHash: "",
      baseRomFilename: "",
      // randomized rom
      rando: {
        fullpath: "",
        filename: "",
        logfullpath: "",
        base64data: "",
        done: false,
        stored: false
      },
      // error
      error: false,
      errorMessage: "",
      // info
      info: false,
      infoMessage: "",
      // options
      optionsLoaded: false,
      defaultLoaded: false,
      defaultOptions: null,
      randomizerOptions: null,

      selectedOptions: {
        seed: null,
        colorscheme: {
          mario: "",
          luigi: "",
          fire: ""
        },
        shuffleLevels: "",
        normalWorldLength: "",
        pipeTransitions: "",
        enemies: "",
        blocks: "",
        bowserAbilities: "",
        bowserHitpoints: "",
        startingLives: "",
        warpZones: "",
        hiddenWarpDestinations: ""
      }
    };
  },

  mounted() {
    // see if there's anything in localforage, update data if so
    localforage.getItem("smbr.rom.randomized.data").then(
      function(value) {
        if (value != null) {
          this.rando.stored = true;
          localforage.getItem("smbr.rom.randomized.filename").then(
            function(value) {
              this.rando.filename = value;
            }.bind(this)
          );
        }
      }.bind(this)
    );
  },

  updated() {},

  created() {
    // Thanks Veetorp!

    // Get all available options from the backend
    axios.get("/randomizer/options").then(response => {
      this.randomizerOptions = response.data;
      this.optionsLoaded = true;
    });

    // Get the default option settings from the backend
    axios.get("/randomizer/options/default").then(response => {
      this.defaultOptions = response.data;
      this.defaultLoaded = true;
      this.loadDefaults();
      this.updateInputted();
    });

    // Look for stored option settings in localforage
    this.loadLocalOptions();

    // Look for stored ROM in localforage, and load it if found.
    localforage.getItem("smbr.rom.base.data").then(function(blob) {
      if (blob == null) {
        EventBus.$emit("noBlob");
        return;
      }
      EventBus.$emit("loadBlob", { target: { files: [new Blob([blob])] } });
    });

    EventBus.$on("update-baserom-filename", this.updateFilename);
    EventBus.$on("store-randomized-rom", this.storeRandomizedRom);
  },

  methods: {
    generateSeed() {
      this.error = false;
      axios
        .post("/randomizer/generate", {
          headers: { "content-type": "multipart/form-data" },
          rom: this.baseRom.getData(),
          romfilename: this.baseRomFilename,
          seed: this.selectedOptions.seed,
          mario: this.selectedOptions.colorscheme.mario,
          luigi: this.selectedOptions.colorscheme.luigi,
          fire: this.selectedOptions.colorscheme.fire,
          shuffleLevels: this.selectedOptions.shuffleLevels,
          normalWorldLength: this.selectedOptions.normalWorldLength,
          pipeTransitions: this.selectedOptions.pipeTransitions,
          enemies: this.selectedOptions.enemies,
          blocks: this.selectedOptions.blocks,
          bowserAbilities: this.selectedOptions.bowserAbilities,
          bowserHitpoints: this.selectedOptions.bowserHitpoints,
          startingLives: this.selectedOptions.startingLives,
          warpZones: this.selectedOptions.warpZones,
          hiddenWarpDestinations: this.selectedOptions.hiddenWarpDestinations
        })
        .then(response => {
          this.rando.fullpath = response.data.fullpath;
          this.rando.filename = response.data.filename;
          this.rando.logfullpath = response.data.logfullpath;
          this.rando.base64data = response.data.base64data;
          this.rando.done = true;
        })
        .then(() => {
          EventBus.$emit("store-randomized-rom");
        })
        .catch(error => {
          if (error.response) {
            switch (error.response.status) {
              case 429:
                this.error = true;
                this.errorMessage = "Whoa, calm down there buddy!";
                break;
              case 500:
                this.error = true;
                this.errorMessage = "Unknown server error!";
                break;
              default:
                this.error = true;
                this.errorMessage =
                  "Generating seed failed! No one knows why :(";
                break;
            }
          }
        });
    },

    updateRom(rom, current_rom_hash) {
      if (!rom) {
        console.log(rom);
        return;
      }

      this.baseRom = rom;
      this.baseRomHash = current_rom_hash;
      this.baseRomLoaded = true;
      localforage.getItem("smbr.rom.base.filename").then(
        function(value) {
          if (value != null) {
            this.baseRomFilename = value;
          }
        }.bind(this)
      );
      this.error = false;
    },

    unloadRom() {
      this.baseRomLoaded = false;
      localforage.removeItem("smbr.rom.base.data").then(function() {});
      localforage.removeItem("smbr.rom.base.filename").then(function() {});
    },

    storeRandomizedRom() {
      // Decode base64 encoded randomized rom data and store in localforage
      var binary_string = Base64.decode(this.rando.base64data);
      var s = "";
      var data = [];

      for (var i = 0; i <= binary_string.length; i++) {
        if (binary_string[i] != " ") {
          s += binary_string[i];
        }

        if (binary_string[i] == " ") {
          data.push(parseInt(s));
          s = "";
        }
      }

      var arr = new Uint8Array(data);
      var newRom = new NewROM(arr.buffer);

      localforage.setItem("smbr.rom.randomized.filename", this.rando.filename);
      localforage.setItem("smbr.rom.randomized.data", newRom.getData());

      this.rando.stored = true;
    },

    saveRandomizedRom() {
      localforage.getItem("smbr.rom.randomized.data").then(function(value) {
        if (value == null) {
          this.error = true;
          this.errorMessage = "No randomized ROM found!";
          return;
        }

        var rom = new NewROM(value);

        localforage
          .getItem("smbr.rom.randomized.filename")
          .then(function(value) {
            rom.save(value);
          });
      });
    },

    updateFilename(filename) {
      this.baseRomFilename = filename;
    },

    updateInputted() {
      this.updateInfoMessage();
      this.checkSelectedOptions();
    },

    updateInfoMessage() {
      this.info = false;
      this.infoMessage = "";
      if (this.selectedOptions.normalWorldLength == "false") {
        this.infoMessage +=
          "<p>When not using normal world lengths, there will be no midway points in any level! In other words, if you die you will always restart at the beginning of the level. This is due to limitations in the original game code.</p>";
        this.info = true;
      }
    },

    checkSelectedOptions() {
      this.error = false;
      this.errorMessage = "";

      if (
        this.selectedOptions.shuffleLevels == "worlds" &&
        this.selectedOptions.normalWorldLength == "false"
      ) {
        this.error = true;
        this.errorMessage +=
          "Invalid combination: 'Shuffle world order only' and 'Worlds can have varying length'";
      }

      if (
        this.selectedOptions.shuffleLevels == "none" &&
        this.selectedOptions.normalWorldLength == "false"
      ) {
        this.error = true;
        this.errorMessage +=
          "Invalid combination: 'Do not shuffle levels' and 'Worlds can have varying length'";
      }

      if (
        this.selectedOptions.pipeTransitions == "keep" &&
        this.selectedOptions.shuffleLevels == "all" &&
        this.selectedOptions.normalWorldLength == "true"
      ) {
        this.error = true;
        this.errorMessage +=
          "Invalid combination: 'Keep pipe transitions', 'Shuffle all levels' and 'Each world has 4 levels'";
      }
    },

    loadDefaults() {
      this.selectedOptions = Object.assign({}, this.defaultOptions);
    },

    loadLocalOptions() {},

    onError(error) {
      this.error = true;
      this.errorMessage = error;
    }
  }
};
</script>