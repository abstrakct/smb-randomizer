<template>
  <div id="smbr-main">
    <b-container fluid>
      <b-row>
        <b-col></b-col>
        <b-col cols="8">
          <b-card
            :title="'Super Mario Bros. Randomizer v' + this.version"
            style="max-width: 150rem;"
          >
            <b-alert
              dismissible
              :show="true"
              variant="info"
            >NOTE: this software is still under development, and currently in a beta/testing stage (at best)! Things might not work out as you expect, but hopefully the worst problem you'll face is a "Server error" message!</b-alert>
            <b-alert
              dismissible
              fade
              :show="error"
              variant="danger"
              v-html="this.errorMessage"
            >Error: {{ this.errorMessage }}</b-alert>

            <b-row>
              <b-col></b-col>
              <b-col></b-col>
            </b-row>

            <smbr-rom-loader v-if="!baseRomLoaded" @update="updateRom" @error="onError"></smbr-rom-loader>

            <p></p>

            <div v-if="baseRomLoaded && optionsLoaded">
              <b-card title="Options" style="max-width: 150rem;">
                <b-row>
                  <b-col>
                    <p>
                      <strong>Current flags: {{ currentFlags }}</strong>
                    </p>
                    <smbr-input
                      id="seed"
                      label="Seed number"
                      @input="updateInputted"
                      storage-key="smbr.opt.seed"
                      v-model="selectedOptions.seed"
                      type="number"
                      placeholder="Input seed number here, or leave blank for random"
                    ></smbr-input>
                    <!-- <smbr-input
                      id="flags"
                      label="Flags"
                      v-model="inputFlags"
                      type="text"
                      @input="updateInputted"
                      placeholder="(NOT IMPLEMENTED) Input flagstring here to set all options from a flag string"
                    ></smbr-input>
                    <b-button @click="applyFlagstring">Apply flagstring</b-button>-->
                    <smbr-select
                      id="olw"
                      label="Level Randomization"
                      @input="updateInputted"
                      storage-key="smbr.opt.levels"
                      v-model="selectedOptions.shuffleLevels"
                      :options="randomizerOptions.shuffleLevels"
                    ></smbr-select>
                    <smbr-select
                      id="owz"
                      label="Warp Zones"
                      @input="updateInputted"
                      storage-key="smbr.opt.warpzones"
                      v-model="selectedOptions.warpZones"
                      :options="randomizerOptions.warpZones"
                    ></smbr-select>
                    <smbr-select
                      id="obl"
                      label="Blocks"
                      @input="updateInputted"
                      storage-key="smbr.opt.blocks"
                      v-model="selectedOptions.blocks"
                      :options="randomizerOptions.blocks"
                    ></smbr-select>
                    <smbr-select
                      id="oen"
                      label="Enemies"
                      @input="updateInputted"
                      storage-key="smbr.opt.enemies"
                      v-model="selectedOptions.enemies"
                      :options="randomizerOptions.enemies"
                    ></smbr-select>
                    <smbr-select
                      id="obh"
                      label="Bowser's Hitpoints"
                      @input="updateInputted"
                      storage-key="smbr.opt.bowserhitpoints"
                      v-model="selectedOptions.bowserHitpoints"
                      :options="randomizerOptions.bowserHitpoints"
                    ></smbr-select>
                    <smbr-select
                      id="osl"
                      label="Starting Lives"
                      @input="updateInputted"
                      storage-key="smbr.opt.startinglives"
                      v-model="selectedOptions.startingLives"
                      :options="randomizerOptions.startingLives"
                    ></smbr-select>
                    <!-- <smbr-select
                      id="ohm"
                      label="Secondary Hard Mode"
                      @input="updateInputted"
                      storage-key="smbr.opt.hardmode"
                      v-model="selectedOptions.hardMode"
                      :options="randomizerOptions.hardMode"
                    ></smbr-select>-->
                  </b-col>

                  <b-col>
                    <smbr-checkbox
                      id="owl"
                      label="Worlds can have varying lengths"
                      @input="updateInputted"
                      storage-key="smbr.opt.normalworldlength"
                      v-model="selectedOptions.normalWorldLength"
                      checked-value="false"
                      unchecked-value="true"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="opt"
                      label="Remove pipe transitions"
                      @input="updateInputted"
                      storage-key="smbr.opt.pipetransitions"
                      v-model="selectedOptions.pipeTransitions"
                      checked-value="remove"
                      unchecked-value="keep"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="ohw"
                      label="Hide warp pipe destinations"
                      @input="updateInputted"
                      storage-key="smbr.opt.hiddenwarpdestinations"
                      v-model="selectedOptions.hiddenWarpDestinations"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="oub"
                      label="Shuffle destinations of pipes going to underground bonus areas"
                      @input="updateInputted"
                      storage-key="smbr.opt.shuffleundergroundbonus"
                      v-model="selectedOptions.shuffleUndergroundBonus"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="oba"
                      label="Randomize where Bowser starts throwing hammers and breathing fire"
                      @input="updateInputted"
                      storage-key="smbr.opt.bowserabilities"
                      v-model="selectedOptions.bowserAbilities"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="ofw"
                      label="Randomize fireworks"
                      @input="updateInputted"
                      storage-key="smbr.opt.fireworks"
                      v-model="selectedOptions.fireworks"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="ofw"
                      label="Randomize brick blocks in underground bonus areas"
                      @input="updateInputted"
                      storage-key="smbr.opt.randomizeundergroundbricks"
                      v-model="selectedOptions.randomizeUndergroundBricks"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="oef"
                      label="Exclude Fire Bars from enemy randomization"
                      @input="updateInputted"
                      v-model="selectedOptions.excludeFirebars"
                      storage-key="smbr.opt.excludefirebars"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="ors"
                      label="Randomize the spin speed of Fire Bars"
                      @input="updateInputted"
                      v-model="selectedOptions.randomizeSpinSpeed"
                      storage-key="smbr.opt.randomizespinspeed"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="ord"
                      label="Shuffle the spin directions of Fire Bars"
                      @input="updateInputted"
                      v-model="selectedOptions.shuffleSpinDirections"
                      storage-key="smbr.opt.shufflespindirections"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="ohko"
                      label="OHKO (One-Hit Knock-Out) mode"
                      @input="updateInputted"
                      v-model="selectedOptions.ohko"
                      storage-key="smbr.opt.ohko"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="orb"
                      label="Randomize background and scenery (EXPERIMENTAL)"
                      @input="updateInputted"
                      storage-key="smbr.opt.randomizebackground"
                      v-model="selectedOptions.randomizeBackground"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-checkbox
                      id="omu"
                      label="Shuffle the music (EXPERIMENTAL, does not change flags/seedhash)"
                      @input="updateInputted"
                      storage-key="smbr.opt.shufflemusic"
                      v-model="selectedOptions.shuffleMusic"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>
                    <smbr-select
                      id="ocsm"
                      label="Mario Color Scheme"
                      @input="updateInputted"
                      storage-key="smbr.opt.mariocolors"
                      v-model="selectedOptions.colorscheme.mario"
                      :options="randomizerOptions.colorscheme.mario"
                    ></smbr-select>
                    <smbr-select
                      id="ocsl"
                      label="Luigi Color Scheme"
                      @input="updateInputted"
                      storage-key="smbr.opt.luigicolors"
                      v-model="selectedOptions.colorscheme.luigi"
                      :options="randomizerOptions.colorscheme.luigi"
                    ></smbr-select>
                    <smbr-select
                      id="ocsf"
                      label="Fire Mario/Luigi Color Scheme"
                      @input="updateInputted"
                      storage-key="smbr.opt.firecolors"
                      v-model="selectedOptions.colorscheme.fire"
                      :options="randomizerOptions.colorscheme.fire"
                    ></smbr-select>
                    <smbr-checkbox
                      id="ovl"
                      label="Generate verbose debug log"
                      @input="updateInputted"
                      storage-key="smbr.opt.verboselog"
                      v-model="selectedOptions.verboseLog"
                      checked-value="true"
                      unchecked-value="false"
                    ></smbr-checkbox>

                    <p></p>

                    <b-button
                      variant="success"
                      @click="generateSeedWithLog"
                      class="w-100"
                    >Generate ROM!</b-button>
                    <p></p>
                    <b-button
                      variant="success"
                      @click="generateSeedNoLog"
                      class="w-100"
                    >Generate ROM without the spoiler log (suitable for races etc.)</b-button>
                    <p></p>
                    <b-button
                      variant="success"
                      @click="generateMysterySeed"
                      class="w-100"
                    >Generate MYSTERY SEED!</b-button>

                    <div v-if="rando.stored">
                      <p></p>

                      <b-button variant="success" @click="saveRandomizedRom" class="w-100">
                        Save
                        <strong>{{ rando.filename }}</strong>
                      </b-button>

                      <p></p>

                      <div v-if="generateLog">
                        <b-button
                          variant="info"
                          class="w-100"
                          :href="rando.logfullpath"
                        >View log (contains spoilers!)</b-button>
                      </div>
                    </div>
                  </b-col>
                </b-row>
              </b-card>
            </div>
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
            <b-button variant="info" href="/about">About/Help</b-button>
            <!-- <b-button @click="loadDefaultsButton">Load default options</b-button> -->
            <p></p>
            <b-alert dismissible :show="info" variant="info" v-html="infoMessage">{{ infoMessage }}</b-alert>
          </b-card>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
export default {
  props: ["version"],

  // TODO: Store selected options in localForage
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
      defaultLoaded: false, // apparently not used?
      defaultOptions: null,
      randomizerOptions: null,
      generateLog: true,
      mysterySeed: false,
      currentFlags: "",
      inputFlags: "",

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
        hiddenWarpDestinations: "",
        fireworks: "",
        shuffleUndergroundBonus: "",
        randomizeBackground: "",
        hardMode: "",
        randomizeUndergroundBricks: "",
        excludeFirebars: "",
        randomizeSpinSpeed: "",
        shuffleSpinDirections: "",
        verboseLog: "",
        shuffleMusic: "",
        ohko: ""
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
    axios
      .get("/randomizer/options/default")
      .then(response => {
        this.defaultOptions = response.data;
        this.defaultLoaded = true;
        this.loadDefaults();
        this.updateInputted();
      })
      .then(() => {
        this.storeDefaultsLocally();
      });

    // if no local option stored
    // store it
    // if local option exists use that
    // Look for stored option settings in localforage

    this.loadLocalOptions();

    // Set flags input box
    this.getFlags();

    // Look for stored ROM in localforage, and load it if found.
    localforage.getItem("smbr.rom.base.data").then(function(blob) {
      if (blob == null) {
        EventBus.$emit("noBlob");
        return;
      }
      EventBus.$emit("loadBlob", { target: { files: [new Blob([blob])] } });
    });

    // Load stored seed number
    localforage.getItem("smbr.opt.seed").then(
      function(value) {
        this.selectedOptions.seed = value;
      }.bind(this)
    );

    // Load stored path to logfile
    localforage.getItem("smbr.rom.randomized.logfilename").then(
      function(value) {
        this.rando.logfullpath = value;
      }.bind(this)
    );

    EventBus.$on("update-baserom-filename", this.updateFilename);
    EventBus.$on("store-randomized-rom", this.storeRandomizedRom);
  },

  methods: {
    generateSeedNoLog() {
      this.generateLog = false;
      this.mysterySeed = false;
      this.generateSeed();
    },

    generateSeedWithLog() {
      this.generateLog = true;
      this.mysterySeed = false;
      this.generateSeed();
    },

    generateMysterySeed() {
      this.generateLog = true;
      this.mysterySeed = true;
      this.generateSeed();
    },

    generateSeed() {
      this.error = false;
      axios
        .post("/randomizer/generate", {
          headers: { "content-type": "multipart/form-data" },
          rom: this.baseRom.getData(),
          romfilename: this.baseRomFilename,
          seed: this.selectedOptions.seed,
          generateLog: this.generateLog,
          mysterySeed: this.mysterySeed,
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
          hiddenWarpDestinations: this.selectedOptions.hiddenWarpDestinations,
          fireworks: this.selectedOptions.fireworks,
          shuffleUndergroundBonus: this.selectedOptions.shuffleUndergroundBonus,
          randomizeBackground: this.selectedOptions.randomizeBackground,
          hardMode: this.selectedOptions.hardMode,
          randomizeUndergroundBricks: this.selectedOptions
            .randomizeUndergroundBricks,
          excludeFirebars: this.selectedOptions.excludeFirebars,
          randomizeSpinSpeed: this.selectedOptions.randomizeSpinSpeed,
          shuffleSpinDirections: this.selectedOptions.shuffleSpinDirections,
          ohko: this.selectedOptions.ohko,
          verboseLog: this.selectedOptions.verboseLog,
          shuffleMusic: this.selectedOptions.shuffleMusic
        })
        .then(response => {
          this.rando.fullpath = response.data.fullpath;
          this.rando.filename = response.data.filename;
          this.rando.logfullpath = response.data.logfullpath;
          this.rando.base64data = response.data.base64data;
          this.rando.done = true;
          localforage.setItem(
            "smbr.rom.randomized.logfilename",
            this.rando.logfullpath
          );
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
                this.errorMessage = "Something went wrong!";
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

      // Make sure the last byte is present and correct
      data.push(0xff);

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
      this.getFlags();
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
        this.selectedOptions.warpZones == "gamble" &&
        this.selectedOptions.hiddenWarpDestinations == "false"
      ) {
        this.error = true;
        this.errorMessage +=
          "Warp Zones Gamble require 'Hide warp pipe destinations' to be selected.<br>";
      }

      if (
        this.selectedOptions.shuffleLevels == "worlds" &&
        this.selectedOptions.normalWorldLength == "false"
      ) {
        this.error = true;
        this.errorMessage +=
          "Invalid combination: 'Shuffle world order only' and 'Worlds can have varying length'<br>";
      }

      if (
        this.selectedOptions.shuffleLevels == "none" &&
        this.selectedOptions.normalWorldLength == "false"
      ) {
        this.error = true;
        this.errorMessage +=
          "Invalid combination: 'Do not shuffle levels' and 'Worlds can have varying length'<br>";
      }

      if (
        this.selectedOptions.pipeTransitions == "keep" &&
        this.selectedOptions.shuffleLevels == "all" &&
        this.selectedOptions.normalWorldLength == "true"
      ) {
        this.error = true;
        this.errorMessage +=
          "Invalid combination: 'Keep pipe transitions', 'Shuffle all levels' and 'Each world has 4 levels'<br>";
      }

      if (
        this.selectedOptions.shuffleLevels == "none" &&
        this.selectedOptions.shuffleUndergroundBonus == "true"
      ) {
        this.error = true;
        this.errorMessage +=
          "Invalid combination: Options 'no level shuffle' and 'shuffle underground bonus areas' does not currently work correctly together.<br>";
      }

      if (this.selectedOptions.randomizeBackground == "true") {
        this.info = true;
        this.infoMessage +=
          "<p><b>Randomize background and scenery</b> is an UNFINISHED, EXPERIMENTAL FEATURE and NOT guaranteed to give good results! Use at your own risk!</p>";
      }
    },

    storeDefaultsLocally() {
      var arr = [
        { key: "smbr.opt.levels", val: this.defaultOptions.shuffleLevels },
        { key: "smbr.opt.warpzones", val: this.defaultOptions.warpZones },
        { key: "smbr.opt.blocks", val: this.defaultOptions.blocks },
        { key: "smbr.opt.enemies", val: this.defaultOptions.enemies },
        {
          key: "smbr.opt.bowserhitpoints",
          val: this.defaultOptions.bowserHitpoints
        },
        {
          key: "smbr.opt.startinglives",
          val: this.defaultOptions.startingLives
        },
        {
          key: "smbr.opt.mariocolors",
          val: this.defaultOptions.colorscheme.mario
        },
        {
          key: "smbr.opt.luigicolors",
          val: this.defaultOptions.colorscheme.luigi
        },
        {
          key: "smbr.opt.firecolors",
          val: this.defaultOptions.colorscheme.fire
        },
        {
          key: "smbr.opt.hiddenwarpdestinations",
          val: this.defaultOptions.hiddenWarpDestinations
        },
        {
          key: "smbr.opt.normalworldlength",
          val: this.defaultOptions.normalWorldLength
        },
        {
          key: "smbr.opt.pipetransitions",
          val: this.defaultOptions.pipeTransitions
        },
        {
          key: "smbr.opt.bowserabilities",
          val: this.defaultOptions.bowserAbilities
        },
        {
          key: "smbr.opt.fireworks",
          val: this.defaultOptions.fireworks
        },
        {
          key: "smbr.opt.shuffleundergroundbonus",
          val: this.defaultOptions.shuffleUndergroundBonus
        },
        {
          key: "smbr.opt.randomizebackground",
          val: this.defaultOptions.randomizeBackground
        },
        {
          key: "smbr.opt.hardmode",
          val: this.defaultOptions.hardMode
        },
        {
          key: "smbr.opt.randomizeundergroundbricks",
          val: this.defaultOptions.randomizeUndergroundBricks
        },
        {
          key: "smbr.opt.excludefirebars",
          val: this.defaultOptions.excludeFirebars
        },
        {
          key: "smbr.opt.randomizespinspeed",
          val: this.defaultOptions.randomizeSpinSpeed
        },
        {
          key: "smbr.opt.shufflespindirections",
          val: this.defaultOptions.shuffleSpinDirections
        },
        {
          key: "smbr.opt.ohko",
          val: this.defaultOptions.ohko
        },
        {
          key: "smbr.opt.verboselog",
          val: this.defaultOptions.verboseLog
        },
        {
          key: "smbr.opt.shufflemusic",
          val: this.defaultOptions.shuffleMusic
        },
        ,
      ];

      arr.forEach(function(entry) {
        localforage.getItem(entry.key).then(
          function(value) {
            if (value == null) {
              localforage.setItem(entry.key, entry.val);
            }
          }.bind(this)
        );
      });
    },

    loadDefaults() {
      this.selectedOptions = Object.assign({}, this.defaultOptions);
    },

    applyFlagstring() {
      axios
        .post("/randomizer/flags/set", { flagstring: this.inputFlags })
        .then(response => {
          console.log(response);
          this.selectedOptions.randomizeBackground = true;
        });
    },

    /* 
 * this isn't working....
    loadDefaultsButton() {
      this.storeDefaultsLocally();
      EventBus.$emit('update-value');
      this.updateInputted();
    },
    */

    loadLocalOptions() {},

    onError(error) {
      this.error = true;
      this.errorMessage = error;
    },

    getFlags() {
      // this.error = false;
      axios
        .post("/randomizer/flags/get", {
          headers: { "content-type": "multipart/form-data" },
          shuffleLevels: this.selectedOptions.shuffleLevels,
          normalWorldLength: this.selectedOptions.normalWorldLength,
          pipeTransitions: this.selectedOptions.pipeTransitions,
          enemies: this.selectedOptions.enemies,
          blocks: this.selectedOptions.blocks,
          bowserAbilities: this.selectedOptions.bowserAbilities,
          bowserHitpoints: this.selectedOptions.bowserHitpoints,
          startingLives: this.selectedOptions.startingLives,
          warpZones: this.selectedOptions.warpZones,
          hiddenWarpDestinations: this.selectedOptions.hiddenWarpDestinations,
          fireworks: this.selectedOptions.fireworks,
          shuffleUndergroundBonus: this.selectedOptions.shuffleUndergroundBonus,
          randomizeBackground: this.selectedOptions.randomizeBackground,
          hardMode: this.selectedOptions.hardMode,
          randomizeUndergroundBricks: this.selectedOptions
            .randomizeUndergroundBricks,
          excludeFirebars: this.selectedOptions.excludeFirebars,
          randomizeSpinSpeed: this.selectedOptions.randomizeSpinSpeed,
          shuffleSpinDirections: this.selectedOptions.shuffleSpinDirections,
          ohko: this.selectedOptions.ohko
          // TODO: why is shuffleMusic here?
          //shuffleMusic: this.selectedOptions.shuffleMusic,
        })
        .then(response => {
          console.log(response);
          this.currentFlags = response.data;
        });
    }
  }
};
</script>
