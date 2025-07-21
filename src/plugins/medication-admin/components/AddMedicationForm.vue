<template>
  <transition name="fade">
    <div v-if="show" class="modal-overlay">
      <template v-if="!isMobile">
        <div class="modal-content">
            <!--<h2 class="modal-title"> -->
                <!-- Add New Medication -->
               <!-- {{ isEditMode ? 'Edit Medication' : 'Add New Medication' }}
            </h2> -->
            <h2 v-if="isAddNewMed==true" class="modal-title">Add New Medication</h2>
            <h2 v-if="isEditMedication==true" class="modal-title">Edit Medication</h2>
            <!-- Tabs Row -->
            <div class="tabs">
                <button
                    v-for="tab in tabs"
                    :key="tab.value"
                    class="tab-button"
                    :class="{ active: activeTab === tab.value }"
                    @click="handleTabClick(tab.value)"
                >
                <!-- Chris version uses @click="activeTab = tab.value" -->
                    {{ tab.label }}
                </button>
            </div>

            <!-- TAB 1: Medication Information -->
            <div v-if="activeTab === 'medInfo'" class="tab-panel">
                <h3 class="section-title">Medication Information</h3>
                <div class="form-group autocomplete">
                    <label>
                        Medication Name <span class="required">*</span>
                    </label>
                    <input
                    type="text"
                    v-model="formData.medicationName"
                    placeholder="Medication Name"
                    required
                    aria-required="true"
                    class="autocomplete-input"
                    @focus="showSuggestions = searchResults.length > 0"
                    @input="debouncedFetch"
                    />
                    <!-- @keyup.prevent="fetchDrugs" -->

                    <!-- <div v-if="drugs.length > 0" class="drug-list">     
                        <div v-for="(drug, index) in drugs" :key="index" class="drug-item" @click="getDrugSynonym(drug[2])">        
                            {{ drug[0] }} - {{ drug[1] }}     
                        </div>   
                    </div> -->
                    <ul v-if="showSuggestions" class="suggestions-list">
                        <li
                          v-for="item in searchResults"
                          :key="item.packaging[0].package_ndc"
                          class="suggestion-item"
                          @mousedown.prevent="selectResult(item)"
                          >
                            <div class="suggestion-header">
                                <strong>
                                    {{ searchMode === 'brand' && item.brand_name
                                        ? item.brand_name
                                        : item.generic_name
                                    }}
                                </strong>
                                <span class="suggestion-ingredients">
                                    ({{ formatIngredients(item.active_ingredients) }})
                                </span>
                            </div>
                            <div class="suggestion-ndc">
                                {{ item.packaging[0].package_ndc }}
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Dosage + Frequency row -->
                <div class="form-row">
                    <div class="form-group dosage-group">
                        <label>
                            Dosage <span class="required">*</span>
                        </label>
                          <div class="dosage-row">
                            <input v-if=" !isEditMedication==true"
                                type="text"
                                v-model="formData.dosage"
                                placeholder="Dosage"
                                class="dosage-input"
                                required
                                aria-required="true"
                            />
                            <input v-if="isEditMedication==true" 
                            type="number" 
                            v-model="formData.dosage" 
                            placeholder="(0)"
                            @change="detectDosageChange(formData.dosage)"/>
                          </div>
                      </div>
                      <div class="form-group">
                            <label>Dosage Form</label>
                            <select
                                v-model="formData.unitType"
                                class="dosage-select"
                            >
                                <option :value="''">Select Dosage Form</option>
                                <option 
                                    v-for="option in dosageOptions"
                                    :key="option"
                                    :value="option"
                                >
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                      <!--</div>-->
                    <div class="form-group">
                        <label>
                            Frequency <span class="required">*</span>
                        </label>
                        <select
                            v-model="formData.frequency"
                            required
                            aria-required="true"
                            @change="isEditMedication==true? detectFreqChange(formData.frequency): formData.frequency"
                        >
                            <option>1 time daily</option>
                            <option>2 times daily</option>
                            <option>3 times daily</option>
                            <option>4 times daily</option>
                            <option>as directed</option>
                            <option>as needed</option>
                            <option>as one dose</option>
                            <option>at bedtime</option>
                            <option>before every meal</option>
                            <option>bi-weekly</option>
                            <option>constant infusion</option>
                            <option>daily</option>
                            <option>daily as directed</option>
                            <option>every day</option>
                            <option>every month</option>
                            <option>every other day</option>
                            <option>every morning</option>
                            <option>every evening</option>
                            <option>every hour</option>
                            <option>every 2 hours</option>
                            <option>every 3 hours</option>
                            <option>every 4 hours</option>
                            <option>every 4 to 6 hours</option>
                            <option>every 4 to 6 minutes</option>
                            <option>every 4 to 8 hours</option>
                            <option>every 6 hours</option>
                            <option>every 8 hours</option>
                            <option>every 12 hours</option>
                            <option>every 24 hours</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                            <option>Saturday</option>
                            <option>Sunday</option>
                            <option>every Monday, Wednesday, Friday, Sunday</option>
                            <option>every Tuesday, Thursday, Saturday</option>
                            <option>before breakfast, lunch, dinner</option>
                            <option>after breakfast, lunch, dinner</option>
                            <option>once a week</option>
                            <option>one time dose</option>
                            <option>three times a week</option>
                            <option>twice daily</option>
                            <option>two times a week</option>
                            <option>use as directed per instructions in pack</option>
                            <option>weekly</option>
                        </select>
                    </div>
                  </div>
                    <div class="form-row admin-times">
                      <div class="form-group">
                          <div v-if="timeInputs.length > 0" class="form-group">
                            <label>Administration Times:</label>
                            <div
                              v-for="(_, index) in timeInputs"
                              :key="index"
                              class="time-input-row"
                            >
                              <input
                                type="time"
                                v-model="timeInputs[index]"
                                class="time-input"
                                required
                              />
                            </div>
                        </div>
              
                      </div>
                      <div class="form-group">
                        <div v-if="ifStatusIsChange==true" >
                          <label>Enter Reason For Change:</label>
                          <div>
                            <input class="change-reason" type="text" placeholder="Enter Reason for Change" v-model="Reasaon4change"/>
                          </div>
                        </div>
                    </div>
                  </div>
                    
                <!--</div>-->

                <!-- Route + Duration row -->
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            Route <span class="required">*</span>
                        </label>
                        <select
                            v-model="formData.route"
                            required
                            aria-required="true"
                            @change="checkRouteSelection(formData.route)"
                        >
                            <option value="">Select route </option>
                            <option value="Oral/Sublingual">Oral/Sublingual</option>
                            <option value="IVI Intravaginal">IVI Intravaginal</option>
                            <option value="SQ (Subcutaneous)">SQ (Subcutaneous)</option>
                            <option value="IM (Intramuscular)">IM (Intramuscular)</option>
                            <option value="IV (Intravenous)">IV (Intravenous)</option>
                            <option value="ID (Intradermal)">ID (Intradermal)</option>
                            <option value="TOP Topical">TOP Topical</option>
                            <option value="Neb/INH">Neb/INH</option>
                            <option value="NAS Intranasal">NAS Intranasal</option>
                            <option value="TD Transdermal">TD Transdermal</option>
                            <option value="Urethral">Urethral</option>
                            <option value="Rectally">Rectally</option>
                            <option value="Optic">Optic</option>
                            <option value="Otic">Otic</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Duration</label>
                        <select v-model="formData.duration">
                            <option value="">Select Duration</option>
                            <option value="7">7 days</option>
                            <option value="14">14 days</option>
                            <option value="30">30 days</option>
                            <option value="60">60 days</option>
                            <option value="90">90 days</option>
                        </select>
                    </div>
                </div>

                <!-- NDC + RxNorm + Diagnosis row -->
                <div class="form-row">
                    <div class="form-group" style="position: relative;">
                        <label>NDC Number</label>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <input
                                type="text"
                                v-model="formData.ndcnumber"
                                placeholder="NDC Number"
                            />
                            <!-- For when scanner is added -->
                            <!-- <button type="button" class="camera-btn" @click="showScanner = true" title="Scan Barcode">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="5" height="5" rx="1.5" stroke="#0c8687" stroke-width="2"/><rect x="16" y="3" width="5" height="5" rx="1.5" stroke="#0c8687" stroke-width="2"/><rect x="16" y="16" width="5" height="5" rx="1.5" stroke="#0c8687" stroke-width="2"/><rect x="3" y="16" width="5" height="5" rx="1.5" stroke="#0c8687" stroke-width="2"/><rect x="8" y="8" width="8" height="8" rx="2" stroke="#0c8687" stroke-width="2"/></svg>
                            </button>
                            <button type="button" class="verify-btn" :disabled="!formData.ndcnumber" @click="fetchMedicationDetailsFromAPI(formData.ndcnumber)">Verify</button> -->
                        </div>
                        <!-- Barcode Scanner Modal, also to be added later -->
                        <!-- <div v-if="showScanner" class="modal-overlay">
                            <div class="modal-content" style="max-width: 420px;">
                                <BarcodeScanner @scanned="onBarcodeScanned" @close="showScanner = false" />
                                <button class="btn-cancel" style="margin-top: 1rem;" @click="showScanner = false">Close</button>
                            </div>
                        </div> -->
                    </div>
                    <div class="form-group">
                        <label>RX Norm</label>
                        <input
                            type="text"
                            v-model="formData.rxnorns"
                            placeholder="RX Norm"
                        />
                    </div>
                    <div class="form-group">
                        <label>Diagnosis</label>
                        <input
                            type="text"
                            v-model="formData.diagnosis"
                            placeholder="Diagnosis"
                            @keyup.prevent="fetchDiagnosis"
                        />
                        <div v-if="newDiagloaded" id="npinamesearch" :class="'selectdisplay-'+newDiagloaded">
                            <div
                                v-for="(dicode, index) in newDiagcodes"
                                class="additionalnameli"
                                :key="index"
                                @click="selectDiagcode(index)"
                            >
                                {{ dicode.code }} - {{ dicode.description }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PRN if route is in [IVI, SQ, IM, ID, TOP] -->
                <div
                    v-if="!['IV (Intravenous)', 'Oral/Sublingual'].includes(formData.route)"
                    class="form-group checkbox-group">
                    <input
                        type="checkbox"
                        id="prnCheck-otherRoutes"
                        v-model="formData.prn"
                    />
                    <label for="prnCheck-otherRoutes">PRN (As Needed)</label>
                </div>

                <!-- Oral route only -->
                <div v-if="formData.route === 'Oral/Sublingual'" class="form-row">
                    <div class="form-group">
                        <label>Number of Tablets/Quantity</label>
                        <input
                            type="number"
                            min="0"
                            v-model.number="formData.quantity"
                        />
                    </div>
                    <div class="form-group checkbox-group">
                        <input
                            type="checkbox"
                            id="prnCheck-oral"
                            v-model="formData.prn"
                        />
                        <label for="prnCheck-oral">PRN (As Needed)</label>
                    </div>
                </div>

                <!-- IV Administration -->
                <div v-if="showIvform==true">
                    <h4>IV Administration</h4>

                    <!-- Fluid Type & VIA row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Fluid Type</label>
                            <select v-model="formData.fluidType">
                                <option value="">Select fluid type</option>
                                <option>0.9% Normal Saline</option>
                                <option>D5W (5% Dextrose in Water)</option>
                                <option>Lactated Ringers (LR)</option>
                                <option>Half Normal Saline (0.45% NaCl)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>VIA</label>
                            <select v-model="formData.via">
                                <option value="">Select an option</option>
                                <option>Peripheral IV - Left Arm</option>
                                <option>Peripheral IV - Right Arm</option>
                                <option>PICC Line - Left</option>
                                <option>PICC Line - Right</option>
                                <option>Mid Line - Left</option>
                                <option>Mid Line - Right</option>
                                <option>Central Line - Left</option>
                                <option>Central Line - Right</option>
                            </select>
                        </div>
                    </div>

                    <!-- Volume + Rate now -->
                    <div class="form-row volume-rate-row">
                        <div class="form-group volume-group">
                            <label>Total Volume</label>
                            <div class="volume-row">
                                <input
                                    list="volumeOptions"
                                    v-model="formData.totalVolume"
                                    class="volume-dropdown"
                                    placeholder="e.g. 100"
                                />
                                <datalist id="volumeOptions">
                                    <option value="10"></option>
                                    <option value="100"></option>
                                    <option value="250"></option>
                                    <option value="500"></option>
                                    <option value="1000"></option>
                                </datalist>
                                <select
                                    class="volume-dropdown"
                                    v-model="formData.totalVolumeUnit"
                                >
                                    <option value="ml">ml</option>
                                    <option value="liter">liter</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Rate</label>
                            <input
                                type="text"
                                v-model="formData.rate"
                                placeholder="50 (ml/hr)"
                            />
                        </div>
                        <div class="form-group">
                            <label>How Long (hrs)</label>
                            <input
                                type="text"
                                :value="formData.howLong"
                                disabled
                                placeholder="Computed"
                            />
                        </div>
                    </div>

                    <!-- Start/End Time row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Start Time</label>
                            <input
                                type="time"
                                v-model="formData.startTime"
                                placeholder="HH:MM"
                            />
                        </div>
                        <div class="form-group">
                            <label>End Time</label>
                            <input
                                type="time"
                                :value="formData.endTime"
                                disabled
                            />
                        </div>
                    </div>

                    <!-- PRN for IV -->
                    <div class="form-group checkbox-group">
                        <input
                            type="checkbox"
                            id="prnCheck-iv"
                            v-model="formData.prn"
                        />
                        <label for="prnCheck-iv">PRN (As Needed)</label>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Prescription Information -->
            <div v-if="activeTab === 'prescriptionInfo'" class="tab-panel">
                <h3 class="section-title">Prescription Information</h3>
                <div class="form-group">
                    <label>RX Number</label>
                    <input
                    type="text"
                    v-model="formData.rxNumber"
                    placeholder="RX Number"
                    />
                </div>
                <div class="form-group">
                    <label>Date the script was filled</label>
                    <!-- If you prefer a date picker, replace with your component -->
                    <input
                    type="date"
                    v-model="formData.filledDate"
                    placeholder="mm/dd/yyyy"
                    />
                </div>
                <div class="form-group">
                    <label>Number of Refills</label>
                    <input
                    type="number"
                    min="0"
                    v-model.number="formData.refills"
                    placeholder="0"
                    />
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input
                            type="date"
                            v-model="formData.startDate"
                            placeholder="mm/dd/yyyy"
                        />
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input
                            type="date"
                            v-model="formData.endDate"
                            placeholder="mm/dd/yyyy"
                        />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Refill Reminder Date</label>
                        <input
                            type="date"
                            v-model="formData.refillReminderDate"
                            placeholder="mm/dd/yyyy"
                        />
                    </div>
                    <div class="form-group">
                        <label>Expiration/Refills until</label>
                        <input
                            type="date"
                            v-model="formData.expirationDate"
                            placeholder="mm/dd/yyyy"
                        />
                    </div>
                </div>
            </div>

            <!-- TAB 3: Provider Information -->
            <div v-if="activeTab === 'providerInfo'" class="tab-panel">
                <h3 class="section-title">Provider Information</h3>
                <div class="form-group">
                    <label>Provider Name</label>
                    <input
                        type="text"
                        v-model="formData.providerName"
                        placeholder="Provider Name"
                        @keyup.prevent="showNpiResult"
                        />
                    <select v-if="pastProvloaded" v-model="loadedProvider" @change="fillProviderInfo" :class="'pastProvdisplay-'+pastProvloaded">
                        <option v-for="(prov,index) in pastProvar" :key="index" >{{ prov.firstname }} {{ prov.lastname }}</option>
                    </select>
                    <div v-if="newProvloaded" id="npinamesearch" :class="'selectdisplay-'+newProvloaded">
                        <div
                        v-for="(npi, index) in newProvider"
                        class="additionalnameli"
                        :key="index + 'npi' + npi.npinumber"
                        @click="selectProvider(index)"
                        >
                            {{ npi.name }} - {{ npi.npinumber }}
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>DEA Number</label>
                        <input
                            type="text"
                            v-model="formData.providerDea"
                            placeholder="DEA Number"
                        />
                    </div>
                    <div class="form-group">
                        <label>NPI Number</label>
                        <input
                            type="text"
                            v-model="formData.providerNpi"
                            placeholder="NPI Number"
                        />
                    </div>
                </div>
                <div class="form-group">
                    <label>License Number</label>
                    <input
                    type="text"
                    v-model="formData.licenseNumber"
                    placeholder="License Number"
                    />
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input
                    type="text"
                    v-model="formData.providerAddress"
                    placeholder="Address"
                    />
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Office Number</label>
                        <input
                            type="text"
                            v-model="formData.providerOffice"
                            placeholder="Office Number"
                        />
                    </div>
                    <div class="form-group">
                        <label>Cell Phone</label>
                        <input
                            type="text"
                            v-model="formData.providerCell"
                            placeholder="Cell Phone"
                        />
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input
                    type="email"
                    v-model="formData.providerEmail"
                    placeholder="Email"
                    />
                </div>
            </div>

            <!-- TAB 4: Pharmacy Information -->
            <div v-if="activeTab === 'pharmacyInfo'" class="tab-panel">
                <h3 class="section-title">Pharmacy Information</h3>
                <div class="form-group">
                    <label>Pharmacy Name</label>
                    <input
                    type="text"
                    v-model="formData.pharmacyName"
                    placeholder="Pharmacy Name"
                    @keyup.prevent="showPharmacyResult"
                    />
                    <div v-if="newpastPatPharcy" id="pharmselect" :class="'selectdisplay2-'+newpastPatPharcy">
                        <select class="pharmselectfield" v-model="loadedpatientNPI" @change="selectpastPharm">
                        <option v-for="(ppharm, index) in  pastpatPharmacy" :key ="index" :value="ppharm.npinumber">
                            {{ ppharm.pharmacyname }} - {{ ppharm.npinumber }}
                        </option>
                        </select>
                    </div>
                    <div v-if="newPharmloaded" id="npinamesearch" :class="'selectdisplay-'+newPharmloaded">
                        <div
                        v-for="(pharm, index) in newPharmacy"
                        class="additionalnameli"
                        :key="index"
                        @click="selectPharm(index)"
                        >
                            {{pharm.npinumber}} - {{pharm.name}} - {{ pharm.addresses[index].address }} 
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>DEA Number</label>
                        <input
                            type="text"
                            v-model="formData.pharmacyDea"
                            placeholder="DEA Number"
                        />
                    </div>
                    <div class="form-group">
                        <label>NPI Number</label>
                        <input
                            type="text"
                            v-model="formData.pharmacyNpi"
                            placeholder="NPI Number"
                        />
                    </div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input
                    type="text"
                    v-model="formData.pharmacyAddress"
                    placeholder="Address"
                    />
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Office Number</label>
                        <input
                            type="text"
                            v-model="formData.pharmacyOffice"
                            placeholder="Office Number"
                        />
                    </div>
                    <div class="form-group">
                        <label>Cell Phone</label>
                        <input
                            type="text"
                            v-model="formData.pharmacyCell"
                            placeholder="Cell Phone"
                        />
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input
                    type="email"
                    v-model="formData.pharmacyEmail"
                    placeholder="Email"
                    />
                </div>
                    <!-- Nurse Signature -->
                <div class="form-group">
                    <label for="nurseSignature">Nurse Signature</label>
                    <input
                        type="text"
                        id="nurseSignature"
                        placeholder="Nurse signature"
                        v-model="formData.nurseSignature"
                    />
                </div>
            </div>

            <!-- Action Buttons (Bottom) -->
            <div class="form-actions">
                <button class="btn-cancel" @click="$emit('close')">
                    Cancel
                </button>
                <button class="btn-save" @click="handleSave" :disabled="!isFormValid">
                    Save
                </button>
            </div>
        </div>
      </template>

      <!-- MOBILE LAYOUT -->
      <template v-else>
        <div class="modal-content mobile-modal">
            <h2 class="modal-title">Add New Medication</h2>

            <!-- Accordions instead of tabs -->
            <details open class="mobile-section">
                <summary class="mobile-section-header">Medication Information</summary>
                <div class="mobile-section-body">
                    
                    <!-- Medication Name + Autocomplete -->
                    <div class="mobile-form-group autocomplete mobile-autocomplete">
                        <label>Medication Name <span class="required">*</span></label>
                        <input
                            type="text"
                            v-model="formData.medicationName"
                            placeholder = "Medication Name"
                            @focus="showSuggestions = searchResults.length > 0"
                            @input="debounedFetch"
                            class="mobile-input"
                        />
                        <ul v-if="showSuggestions" class="mobile-suggestions-list">
                            <li
                                v-for="item in searchResults"
                                :key="item.packaging[0].package_ndc"
                                class="mobile-suggestion-item"
                                @mousedown.prevent="selectResult(item)"
                            >
                                <div class="mobile-suggestion-header">
                                    <strong>
                                        {{ searchMode === 'brand' && item.brand_name 
                                            ? item.brand_name 
                                            : item.generic_name }}
                                    </strong>
                                    <small class="mobile-suggestion-ingredients">
                                        {{ formatIngredients(item.active_ingredients) }}
                                    </small>
                                </div>
                                <div class="mobile-suggestion-ndc">
                                    {{ item.packaging[0].package_ndc }}
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Dosage + Dosage Form -->
                    <div class="mobile-form-group">
                        <label>Dosage <span class="required">*</span></label>
                        <div class="mobile-inline-group">
                            <input
                            type="text"
                            v-model="formData.dosage"
                            placeholder="Dosage"
                            class="mobile-input dosage-input"
                            />
                            <select v-model="formData.unitType" class="mobile-select">
                            <option value="">Select Dosage Form</option>
                            <option v-for="opt in dosageOptions" :key="opt">{{ opt }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Frequency -->
                    <div class="mobile-form-group">
                        <label>Frequency <span class="required">*</span></label>
                        <select v-model="formData.frequency" class="mobile-select">
                            <option value="">Select frequency</option>
                            <option v-for="opt in frequencyOptions" :key="opt">{{ opt }}</option>
                        </select>
                    </div>

                    <!-- Route + Duration -->
                    <div class="mobile-form-group">
                        <label>Route <span class="required">*</span></label>
                        <select v-model="formData.route" class="mobile-select">
                            <option value="">Select route</option>
                            <option v-for="opt in routeOptions" :key="opt" :value="opt">{{ opt }}</option>
                        </select>
                    </div>
                    <div class="mobile-form-group">
                        <label>Duration</label>
                        <select v-model="formData.duration" class="mobile-select">
                            <option value="">Select Duration</option>
                            <option value="7">7 days</option>
                            <option value="14">14 days</option>
                            <option value="30">30 days</option>
                            <option value="60">60 days</option>
                            <option value="90">90 days</option>
                        </select>
                    </div>

                    <!-- NDC + Scanner + Verify -->
                    <div class="mobile-form-group">
                        <label>NDC Number</label>
                        <div class="mobile-inline-group">
                            <input
                            type="text"
                            v-model="formData.ndcnumber"
                            placeholder="NDC Number"
                            class="mobile-input"
                            />
                            <button @click="showScanner = true" class="mobile-icon-btn" title="Scan Barcode">
                                <!-- replace with your SVG icon -->
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                    stroke="#0c8687" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3"  y="3"  width="5"  height="5"  rx="1.5"/>
                                    <rect x="16" y="3"  width="5"  height="5"  rx="1.5"/>
                                    <rect x="16" y="16" width="5"  height="5"  rx="1.5"/>
                                    <rect x="3"  y="16" width="5"  height="5"  rx="1.5"/>
                                    <rect x="8"  y="8"  width="8"  height="8"  rx="2"/>
                                </svg>
                            </button>
                            <button
                            @click="fetchMedicationDetailsFromAPI(formData.ndcnumber)"
                            :disabled="!formData.ndcnumber"
                            class="mobile-verify-btn"
                            >
                            Verify
                            </button>
                        </div>

                        <!-- inline modal for mobile scanner -->
                        <div v-if="showScanner" class="modal-overlay">
                            <div class="modal-content" style="max-width:360px;">
                                <!-- <BarcodeScanner
                                    :active="showScanner"
                                    :scanRegion="scanRegion"
                                    :rapidScanMode="rapidScanMode"
                                    @scanned="onBarcodeScanned"
                                    @close="showScanner = false"
                                /> -->
                                <button class="btn-cancel" @click="showScanner = false" style="margin-top:1rem">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- RX Norm -->
                    <div class="mobile-form-group">
                        <label>RX Norm</label>
                        <input
                            type="text"
                            v-model="formData.rxnorns"
                            placeholder="RX Norm"
                            class="mobile-input"
                        />
                    </div>

                    <!-- Diagnosis -->
                    <div class="mobile-form-group">
                        <label>Diagnosis</label>
                        <input
                            type="text"
                            v-model="formData.diagnosis"
                            placeholder="Diagnosis"
                            class="mobile-input"
                        />
                    </div>

                    <!-- PRN Checkbox for non-oral routes -->
                    <div
                        v-if="!['IV (Intravenous)','Oral/Sublingual'].includes(formData.route)"
                        class="mobile-form-group mobile-checkbox-group"
                        >
                        <input type="checkbox" id="prn-other" v-model="formData.prn" />
                        <label for="prn-other">PRN (As Needed)</label>
                    </div>

                    <!-- Oral ONLY quantity + PRN -->
                    <div v-if="formData.route === 'Oral/Sublingual'" class="mobile-form-group">
                        <label>Quantity (# Tablets)</label>
                        <input
                            type="number"
                            v-model.number="formData.quantity"
                            min="0"
                            class="mobile-input"
                        />
                        <div class="mobile-checkbox-group">
                            <input id="prn-oral" type="checkbox" v-model="formData.prn" />
                            <label for="prn-oral">PRN (As Needed)</label>
                        </div>
                    </div>

                    <!-- IV Subsection -->
                    <div v-if="formData.route === 'IV (Intravenous)'" class="mobile-subsection">
                        <h4 class="mobile-subsection-title">IV Administration</h4>

                        <div class="mobile-form-group">
                            <label>Fluid Type</label>
                            <select v-model="formData.fluidType" class="mobile-select">
                                <option value="">Select fluid type</option>
                                <option>0.9% Normal Saline</option>
                                <option>D5W (5% Dextrose in Water)</option>
                                <option>Lactated Ringers (LR)</option>
                                <option>Half Normal Saline (0.45% NaCl)</option>
                            </select>
                        </div>
                        <div class="mobile-form-group">
                            <label>VIA</label>
                            <select v-model="formData.via" class="mobile-select">
                                <option value="">Select IV site</option>
                                <option>Peripheral IV – Left Arm</option>
                                <option>Peripheral IV – Right Arm</option>
                                <option>PICC Line – Left</option>
                                <option>PICC Line – Right</option>
                                <option>Mid Line – Left</option>
                                <option>Mid Line – Right</option>
                                <option>Central Line – Left</option>
                                <option>Central Line – Right</option>
                            </select>
                        </div>

                        <div class="mobile-form-group">
                            <label>Total Volume</label>
                            <div class="mobile-inline-group">
                                <input
                                    list="volumeOptions"
                                    v-model="formData.totalVolume"
                                    placeholder="e.g. 100"
                                    class="mobile-input volume-input"
                                />
                                <select v-model="formData.totalVolumeUnit" class="mobile-select">
                                    <option value="ml">ml</option>
                                    <option value="liter">liter</option>
                                </select>
                            </div>
                        </div>

                        <div class="mobile-form-group">
                            <label>Rate (ml/hr)</label>
                            <input
                            type="number"
                            v-model="formData.rate"
                            placeholder="50"
                            class="mobile-input"
                            />
                        </div>

                        <div class="mobile-form-group">
                            <label>How Long (hrs)</label>
                            <input
                            type="text"
                            :value="formData.howLong"
                            disabled
                            class="mobile-input"
                            />
                        </div>

                        <div class="mobile-form-group">
                            <label>Start Time</label>
                            <input type="time" v-model="formData.startTime" class="mobile-input" />
                        </div>
                        <div class="mobile-form-group">
                            <label>End Time</label>
                            <input type="time" :value="formData.endTime" disabled class="mobile-input" />
                        </div>

                        <div class="mobile-checkbox-group">
                            <input type="checkbox" id="prn-iv" v-model="formData.prn" />
                            <label for="prn-iv">PRN (As Needed)</label>
                        </div>
                    </div>
                </div>
            </details>

            <details open class="mobile-section">
                <summary class="mobile-section-header">Prescription Information</summary>
                <div class="mobile-section-body">
                    <!-- RX Number -->
                    <div class="mobile-form-group">
                        <label>RX Number</label>
                        <input
                            type="text"
                            v-model="formData.rxNumber"
                            placeholder="RX Number"
                            class="mobile-input"
                        />
                    </div>

                    <!-- Date Script Filled -->
                    <div class="mobile-form-group">
                        <label>Date Script Was Filled</label>
                        <input
                            type="date"
                            v-model="formData.filledDate"
                            placeholder="mm/dd/yyyy"
                            class="mobile-input"
                        />
                    </div>

                    <!-- Number of Refills -->
                    <div class="mobile-form-group">
                        <label>Number of Refills</label>
                        <input
                            type="number"
                            min="0"
                            v-model.number="formData.refills"
                            placeholder="0"
                            class="mobile-input"
                        />
                    </div>

                    <!-- Start & End Dates -->
                    <div class="mobile-inline-group">
                        <div class="mobile-form-group">
                            <label>Start Date</label>
                            <input
                            type="date"
                            v-model="formData.startDate"
                            placeholder="mm/dd/yyyy"
                            class="mobile-input"
                            />
                        </div>
                        <div class="mobile-form-group">
                            <label>End Date</label>
                            <input
                            type="date"
                            v-model="formData.endDate"
                            placeholder="mm/dd/yyyy"
                            class="mobile-input"
                            />
                        </div>
                    </div>

                    <!-- Refill Reminder & Expiration -->
                    <div class="mobile-inline-group">
                        <div class="mobile-form-group">
                            <label>Refill Reminder Date</label>
                            <input
                            type="date"
                            v-model="formData.refillReminderDate"
                            placeholder="mm/dd/yyyy"
                            class="mobile-input"
                            />
                        </div>
                        <div class="mobile-form-group">
                            <label>Expiration / Refills Until</label>
                            <input
                            type="date"
                            v-model="formData.expirationDate"
                            placeholder="mm/dd/yyyy"
                            class="mobile-input"
                            />
                        </div>
                    </div>
                </div>
            </details>

            <details open class="mobile-section">
                <summary class="mobile-section-header">Provider Information</summary>
                <div class="mobile-section-body">
                    <!-- Provider Name -->
                    <div class="mobile-form-group">
                        <label>Provider Name</label>
                        <input
                            type="text"
                            v-model="formData.providerName"
                            placeholder="Provider Name"
                            class="mobile-input"
                        />
                    </div>

                    <!-- DEA & NPI -->
                    <div class="mobile-inline-group">
                        <div class="mobile-form-group">
                            <label>DEA Number</label>
                            <input
                            type="text"
                            v-model="formData.providerDea"
                            placeholder="DEA Number"
                            class="mobile-input"
                            />
                        </div>
                        <div class="mobile-form-group">
                            <label>NPI Number</label>
                            <input
                            type="text"
                            v-model="formData.providerNpi"
                            placeholder="NPI Number"
                            class="mobile-input"
                            />
                        </div>
                    </div>

                    <!-- License Number -->
                    <div class="mobile-form-group">
                        <label>License Number</label>
                        <input
                            type="text"
                            v-model="formData.licenseNumber"
                            placeholder="License Number"
                            class="mobile-input"
                        />
                    </div>

                    <!-- Address -->
                    <div class="mobile-form-group">
                        <label>Address</label>
                        <input
                            type="text"
                            v-model="formData.providerAddress"
                            placeholder="Address"
                            class="mobile-input"
                        />
                    </div>

                    <!-- Office & Cell -->
                    <div class="mobile-inline-group">
                        <div class="mobile-form-group">
                            <label>Office Number</label>
                            <input
                            type="text"
                            v-model="formData.providerOffice"
                            placeholder="Office Number"
                            class="mobile-input"
                            />
                        </div>
                        <div class="mobile-form-group">
                            <label>Cell Phone</label>
                            <input
                            type="text"
                            v-model="formData.providerCell"
                            placeholder="Cell Phone"
                            class="mobile-input"
                            />
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mobile-form-group">
                        <label>Email</label>
                        <input
                            type="email"
                            v-model="formData.providerEmail"
                            placeholder="Email"
                            class="mobile-input"
                        />
                    </div>
                </div>
            </details>

            <details open class="mobile-section">
                <summary class="mobile-section-header">Pharmacy Information</summary>
                <div class="mobile-section-body">

                    <!-- Pharmacy Name -->
                    <div class="mobile-form-group">
                        <label>Pharmacy Name</label>
                        <input
                            type="text"
                            v-model="formData.pharmacyName"
                            placeholder="Pharmacy Name"
                            class="mobile-input"
                        />
                    </div>

                    <!-- DEA & NPI -->
                    <div class="mobile-inline-group">
                        <div class="mobile-form-group">
                            <label>DEA Number</label>
                            <input
                            type="text"
                            v-model="formData.pharmacyDea"
                            placeholder="DEA Number"
                            class="mobile-input"
                            />
                        </div>
                        <div class="mobile-form-group">
                            <label>NPI Number</label>
                            <input
                            type="text"
                            v-model="formData.pharmacyNpi"
                            placeholder="NPI Number"
                            class="mobile-input"
                            />
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mobile-form-group">
                        <label>Address</label>
                        <input
                            type="text"
                            v-model="formData.pharmacyAddress"
                            placeholder="Address"
                            class="mobile-input"
                        />
                    </div>

                    <!-- Office & Cell -->
                    <div class="mobile-inline-group">
                        <div class="mobile-form-group">
                            <label>Office Number</label>
                            <input
                            type="text"
                            v-model="formData.pharmacyOffice"
                            placeholder="Office Number"
                            class="mobile-input"
                            />
                        </div>
                        <div class="mobile-form-group">
                            <label>Cell Phone</label>
                            <input
                            type="text"
                            v-model="formData.pharmacyCell"
                            placeholder="Cell Phone"
                            class="mobile-input"
                            />
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mobile-form-group">
                        <label>Email</label>
                        <input
                            type="email"
                            v-model="formData.pharmacyEmail"
                            placeholder="Email"
                            class="mobile-input"
                        />
                    </div>

                    <!-- Nurse Signature -->
                    <div class="mobile-form-group">
                        <label for="nurseSignature">Nurse Signature</label>
                        <input
                            type="text"
                            id="nurseSignature"
                            v-model="formData.nurseSignature"
                            placeholder="Nurse signature"
                            class="mobile-input"
                        />
                    </div>
                </div>
            </details>
            <!-- Save/Cancel -->
            <div class="form-actions mobile-actions">
                <button class="btn-cancel" @click="handleCancel">Cancel</button>
                <button class="btn-save" @click="handleSave" :disabled="!isFormValid">
                Save
                </button>
            </div>
        </div>
      </template>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref, toRefs, computed, watch, defineProps, defineEmits, onMounted, onUnmounted } from 'vue'
import { PastProvarItem } from '../types';
import axios from 'axios';
// import BarcodeScanner from "./barcode-scanner/BarcodeScanner.vue"

/** Define the structure of all form fields. */
/*interface MedicationFormData {
  medicationName: string;
  ndcnumber: string;
  rxnorns: string;
  diagnosis: string;
  diagdescription:string;
  dosage: string;
  frequency: string;
  route: string;
  prn: boolean;
  quantity: number;

  rxNumber: string;
  filledDate: string;
  refills: number;
  startDate: string;
  endDate: string;
  refillReminderDate: string;
  expirationDate: string;

  providerName: string;
  providerDea: string;
  providerNpi: string;
  licenseNumber: string;
  providerAddress: string;
  providerOffice: string;
  providerCell: string;
  providerEmail: string;

  pharmacyName: string;
  pharmacyDea: string;
  pharmacyNpi: string;
  pharmacyAddress: string;
  pharmacyOffice: string;
  pharmacyCell: string;
  pharmacyEmail: string;

  // Chris additions //
  unitType: string;
  fluidType: string; 
  totalVolume: string;
  totalVolumeUnit: string;
  rate: string;
  howLong: string;
  startTime: string;
  endTime: string;
  via: string;
  sqInjectionSite: string;
  idInjectionSite: string;
  imInjectionSite: string;
  nurseSignature: string;
} */
interface MedicationFormData {
  medicationName: string;
  ndcnumber: string;
  rxnorns: string;
  diagnosis: string;
  diagdescription:string;
  dosage: string;
  frequency: string;
  route: string;
  administrationTimes?: string; //new to the form it hold the med times
  coreason:string, //new to the form and it holds the reason for the change
  ordernumber:string, //new paramater that needs to be present when the parent component passis formdata items to axios for processing 
  prn: boolean;
  quantity: number;
  rate:'';
  unitType:'';
  duration: '';
  fluidType: '';
  totalVolume: '';
  totalVolumeUnit: 'ml';
  howLong: '';
  startTime: '';
  endTime: '';
  via: '';
  tabletnumber:'';
  sqInjectionSite: '';
  idInjectionSite: '';
  imInjectionSite: '';
  nurseSignature:'';
  rxNumber: string;
  filledDate: string;
  refills: number;
  startDate: string;
  endDate: string;
  refillReminderDate: string;
  expirationDate: string;

  providerName: string;
  providerDea: string;
  providerNpi: string;
  licenseNumber: string;
  providerAddress: string;
  providerOffice: string;
  providerCell: string;
  providerEmail: string;

  pharmacyName: string;
  pharmacyDea: string;
  pharmacyNpi: string;
  pharmacyAddress: string;
  pharmacyOffice: string;
  pharmacyCell: string;
  pharmacyEmail: string;
}

// --------------- Chris additions to <script> ---------------

interface ActiveIngredient {
    name: string
    strength: string
}

interface FdaResult {
    generic_name: string
    brand_name?: string
    active_ingredients?: ActiveIngredient[]
    packaging: Array<{
        description: string
        package_ndc: string
    }>
    dosage_form: string
    route: string[]
}
//----Moving Initialization up------------------//
/**
 * Props:
 *  show: controls visibility of the modal
 */
 const props = defineProps<{
  show: boolean;
  pastProvloaded:boolean;
  pastProvar: PastProvarItem[];
  //Merge code attempt7/15
  isEditMedication:boolean;
  isAddNewMed:boolean;
  ifStatusIsChange:boolean;
  editFormdata: Partial<MedicationFormData> | null; //object ;
  // Chris additions
  //existingMedication?: Partial<MedicationFormData> | null; - chris
}>()
//Merge code attemp 7/15
const selectedDosage = ref<string>('');
const selectedFrequency = ref<string>('');
const timeInputs = ref<string[]>([]);
// Keyon Added variables for Time Change - Status change
const ifStatusIsChange = ref<boolean>(false);
const Reasaon4change = ref<string>('');
/**
 * Emits:
 *  close  -> for closing/canceling the modal
 *  save   -> sends the entire formData object
 */
const emit = defineEmits<{
  (e: 'close'): void;
//   (e: 'save', payload: MedicationFormData): void;
  (e: 'save', payload: MedicationFormData & { isEdit: boolean }): void;
  (e: 'freqchange', payload: MedicationFormData,selectedFrequency:string): void;
  (e: 'dosagechange', payload:MedicationFormData,selectedDosage:string): void;
  (e: 'loadprov'): void;
  (e: 'updtPastProvbool'):void;
}>()
/** Reacctive oject for exetracing and prepopulating form from past Med data */
const { editFormdata } = toRefs(props); 
const frequencyOptions = ['1 time daily','2 times daily',
  '3 times daily','4 times daily',
  'as directed','as needed','as one dose',
  'at bedtime',
  'before every meal','bi-weekly','constant infusion','daily',
  'daily as directed','every day','every month','every other day','every morning','every evening',
  'every hour','every 2 hours',
  'every 3 hours','every 4 hours',
  'every 4 to 6 hours, as needed','every 4 to 6 minutes','every 4 to 8 hours',
  'every 6 hours',
  'every 8 hours','every 12 hours',
  'every 24 hours',
  'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday',
  'every Monday, Wednesday, Friday, Sunday',
  'every Tuesday, Thursday, Saturday','before breakfast, lunch, dinner','after breakfast, lunch, dinner',
  'once a week','one time dose',
  'three times a week','twice daily',
  'two times a week','use as directed per instructions in pack','weekly']

const routeOptions = [
    'Oral/Sublingual',
    'IVI Intravaginal',
    'SQ (Subcutaneous)',
    'IM (Instramuscular)',
    'IV (Intravenous)',
    'ID (Intradermal)',
    'TOP Topical',
    'Neb/INH',
    'NAS Intranasal',
    'TD Transdermal',
    'Urethral',
    'Rectally',
    'Optic',
    'Otic'
]

// Map of keywords ? exactly your <option> values
const routeMap: Record<string, string> = {
  oral:                'Oral/Sublingual',
  sublingual:          'Oral/Sublingual',
  vaginal:             'IVI Intravaginal',
  intravaginal:        'IVI Intravaginal',
  subcutaneous:        'SQ (Subcutaneous)',
  sq:                  'SQ (Subcutaneous)',
  sc:                  'SQ (Subcutaneous)',
  intramuscular:       'IM (Intramuscular)',
  im:                  'IM (Intramuscular)',
  intravenous:         'IV (Intravenous)',
  iv:                  'IV (Intravenous)',
  id:                  'ID (Intradermal)',
  intradermal:         'ID (Intradermal)',
  topical:             'TOP Topical',
  neb:                 'Neb/INH',
  inh:                 'Neb/INH',
  intranasal:          'NAS Intranasal',
  nasal:               'NAS Intranasal',
  transdermal:         'TD Transdermal',
  urethral:            'Urethral',
  rectal:              'Rectally',
  optic:               'Optic',
  otic:                'Otic',
};

// 1) state
const searchResults = ref<FdaResult[]>([])
const showSuggestions = ref(false)
const searchMode      = ref<'brand' | 'generic'>('brand')
let debounceTimer: number

function formatIngredients(ings?: ActiveIngredient[]): string {
  if (!ings || ings.length === 0) return ''
  return ings
    .map((i) => `${i.name}: ${i.strength}`)
    .join(', ')
}

// 2) fetch & parse
async function fetchSuggestions() {
  const q = formData.value.medicationName.trim()
  if (!q) {
    searchResults.value = []
    showSuggestions.value = false
    return
  }

  try {
    // 1) brand_name search
    searchMode.value = 'brand'
    let resp = await fetch(
      `https://api.fda.gov/drug/ndc.json?search=brand_name:${encodeURIComponent(q)}*&limit=10`
    )
    let data = await resp.json()

    // 2) fallback to generic_name if no brand results
    if (!Array.isArray(data.results) || data.results.length === 0) {
      searchMode.value = 'generic'
      resp = await fetch(
        `https://api.fda.gov/drug/ndc.json?search=generic_name:${encodeURIComponent(q)}*&limit=10`
      )
      data = await resp.json()
    }

    // 3) store up to 10 entries
    searchResults.value = Array.isArray(data.results)
      ? (data.results as FdaResult[]).slice(0, 10)
      : []
    showSuggestions.value = searchResults.value.length > 0
  } catch (err) {
    console.error('FDA lookup error', err)
    searchResults.value = []
    showSuggestions.value = false
  }
}

// 3) debounce wrapper
function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = window.setTimeout(fetchSuggestions, 300)
}

function normalizeRoute(apiRoute: string): string {
  const r = apiRoute.toLowerCase();
  for (const key in routeMap) {
    if (r.includes(key)) {
      return routeMap[key];
    }
  }
  return '';
}

function selectResult(item: FdaResult) {
  // Determine display name (brand vs generic)
  const baseName =
    searchMode.value === 'brand' && item.brand_name
      ? item.brand_name
      : item.generic_name;

  // Format ingredients string
  const ingStr = formatIngredients(item.active_ingredients);
  const displayName = ingStr
    ? `${baseName} (${ingStr})`
    : baseName;

  // Populate the Medication Name input
  formData.value.medicationName = displayName;

  // NDC
  formData.value.ndcnumber = item.packaging[0].package_ndc;

  // Dosage form ? unitType (capitalized)
  const rawDf = item.dosage_form || '';
  const dfBase = rawDf.split(',')[0].trim().toLowerCase();
  formData.value.unitType = dfBase.charAt(0).toUpperCase() + dfBase.slice(1);

  // Route mapping
  const candidate =
    Array.isArray(item.route) && item.route.length
      ? item.route[0]
      : '';
  formData.value.route = normalizeRoute(candidate);

  // Hide dropdown
  showSuggestions.value = false;
}

const dosageOptions = [
  "Actuation","Ampule","Application","Applicator","Auto-Injector","Bar","Capful","Caplet","Capsule",
  "Cartridge","Centimeter","Disk","Dropperful","Each","Film","Fluid Ounce","Gallon","Gram","Gum","Implant",
  "Inch","Inhalation","Injection","Insert","Liter","Lollipop","Lozenge","Metric Drop","Microgram",
  "Milliequivalent","Milligram","Milliliter","Nebule","Ounce","Package","Packet","Pad","Patch","Pellet",
  "Pill","Pint","Pre-filled Pen Syringe","Puff","Pump","Ring","Sachet","Scoopful","Sponge","Spray","Stick",
  "Strip","Suppository","Swab","Syringe","Tablet","Troche","Unit","Vial","Wafer"
]
const formData = ref<MedicationFormData>({
  medicationName: '',
  ndcnumber: '',
  rxnorns: '',
  diagnosis: '',
  diagdescription:'',
  dosage: '',
  frequency: '',
  administrationTimes:'', //added because of the Update so this is new
  coreason:'', //add because of the update so this is new
  ordernumber:'',//added because the update - this is new 
  route: 'Oral/Sublingual',
  prn: false,
  quantity: 0,
  rate:'',
  unitType:'',
  duration: '',
  fluidType: '',
  totalVolume: '',
  totalVolumeUnit: 'ml',
  rate: '',
  howLong: '',
  startTime: '',
  endTime: '',
  via: '',
  tabletnumber:'',
  sqInjectionSite: '',
  idInjectionSite: '',
  imInjectionSite: '',
  nurseSignature:'',
  rxNumber: '',
  filledDate: '',
  refills: 0,
  startDate: '',
  endDate: '',
  refillReminderDate: '',
  expirationDate: '',

  providerName: '',
  providerDea: '',
  providerNpi: '',
  licenseNumber: '',
  providerAddress: '',
  providerOffice: '',
  providerCell: '',
  providerEmail: '',

  pharmacyName: '',
  pharmacyDea: '',
  pharmacyNpi: '',
  pharmacyAddress: '',
  pharmacyOffice: '',
  pharmacyCell: '',
  pharmacyEmail: ''
})
//const isEditMode = computed(() => !!props.existingMedication)

// Require name, dosage, frequency, and route
const isFormValid = computed(() =>
  formData.value.medicationName.trim() !== '' &&
  formData.value.dosage.trim() !== '' ||
  formData.value.totalVolume !=="" &&
  formData.value.frequency !== '' &&
  formData.value.route !== ''
)



function resetForm() {
  formData.value = {
    medicationName: '',
    ndcnumber: '',
    rxnorns: '',
    diagnosis: '',
    dosage: '',
    unitType: '',
    frequency: '',
    route: '',
    duration: '',
    fluidType: '',
    prn: false,
    quantity: 0,

    totalVolume: '',
    totalVolumeUnit: 'ml',
    rate:'',
    howLong: '',
    startTime: '',
    endTime: '',
    via: '',

    sqInjectionSite: '',
    idInjectionSite: '',
    imInjectionSite: '',

    rxNumber: '',
    filledDate: '',
    refills: 0,
    startDate: '',
    endDate: '',
    refillReminderDate: '',
    expirationDate: '',

    providerName: '',
    providerDea: '',
    providerNpi: '',
    licenseNumber: '',
    providerAddress: '',
    providerOffice: '',
    providerCell: '',
    providerEmail: '',

    pharmacyName: '',
    pharmacyDea: '',
    pharmacyNpi: '',
    pharmacyAddress: '',
    pharmacyOffice: '',
    pharmacyCell: '',
    pharmacyEmail: '',

    nurseSignature: ''
  }
}

const isMobile = ref(window.innerWidth < 768)
function updateMobile() {
  isMobile.value = window.innerWidth < 768
}
onMounted(() => {
  window.addEventListener('resize', updateMobile)
})
onUnmounted(() => {
  window.removeEventListener('resize', updateMobile)
})
function selectedFreqchange()
{
  
}
function handleCancel() {
  emit('close')
  resetForm();
 
}

watch(
  [() => formData.value.totalVolume, () => formData.value.rate, () => formData.value.totalVolumeUnit],
  () => {
    const vol = parseFloat(formData.value.totalVolume) || 0
    let numericRate = parseFloat(formData.value.rate) || 0
    const mrate =0;
    if (!numericRate) {
    
      const match =formData.value.rate.match(/(\d+(\.\d+)?)/);
      if (match) numericRate = parseFloat(match[1])
    }
    const finalVolumeInMl =
      formData.value.totalVolumeUnit === 'liter' ? vol * 1000 : vol
    let hours = numericRate > 0 ? finalVolumeInMl / numericRate : 0
    formData.value.howLong = hours > 0 ? hours.toFixed(2) : ''
  }
)

watch(
  [() => formData.value.howLong, () => formData.value.startTime],
  () => {
    if (!formData.value.howLong || !formData.value.startTime) {
      formData.value.endTime = ''
      return
    }
    const [startH, startM] = formData.value.startTime.split(':').map(Number)
    const hoursFloat = parseFloat(formData.value.howLong)
    if (isNaN(hoursFloat) || isNaN(startH)) {
      formData.value.endTime = ''
      return
    }
    const totalMinutes = Math.round(hoursFloat * 60)
    let newH = startH
    let newM = startM + totalMinutes
    newH += Math.floor(newM / 60)
    newM = newM % 60
    const hh = String(newH % 24).padStart(2, '0')
    const mm = String(newM).padStart(2, '0')
    formData.value.endTime = `${hh}:${mm}`
  }
)


// ---------- FREQUENCY WATCH ----------//
watch(selectedFrequency, (newFreq) => {
  if (!newFreq || (editFormdata.value && editFormdata.prn)) {
    timeInputs.value = []
    return
  }
  const timesCount = getTimesCountFromFrequency(newFreq)
  timeInputs.value = Array(timesCount).fill('')
},{ deep: true })

function getTimesCountFromFrequency(frequency: string): number {
  if (!frequency) return 0
  const dailyMatch = frequency.match(/(\d+)\s*times?\s*daily/)
  if (dailyMatch) {
    return parseInt(dailyMatch[1], 10)
  }
  const hoursMatch = frequency.match(/every\s*(\d+)\s*hours?/)
  if (hoursMatch) {
    const hours = parseInt(hoursMatch[1], 10)
    return Math.floor(24 / hours)
  }
  switch (frequency) {
    case 'every hour': return 24
    case 'daily': return 1
    case 'at bedtime': return 1
    case 'every 24 hours': return 1
    case 'every other day': return 4
    case 'monday, wednesday, friday, sunday': return 4
    case 'tuesday, thursday, saturday': return 3
    default: return 1
  }
}
const showScanner = ref(false)

async function fetchMedicationDetailsFromAPI(ndc) {
  try {
    // Remove dashes if present, as openFDA expects 10- or 11-digit NDCs without dashes
    const cleanNdc = ndc.replace(/-/g, '');
    const response = await fetch(`https://api.fda.gov/drug/ndc.json?search=product_ndc:${cleanNdc}`);
    if (!response.ok) throw new Error('API request failed');
    const data = await response.json();
    if (data.results && data.results.length > 0) {
      const med = data.results[0];
      // Example: autofill medication name and show info
      formData.value.medicationName = med.brand_name || med.generic_name || '';
      alert(`Medication found: ${med.brand_name || med.generic_name || 'Unknown'}\nLabeler: ${med.labeler_name || 'Unknown'}`);
    } else {
      alert('No medication details found for this NDC.');
    }
  } catch (err) {
    alert('Error fetching medication details: ' + (err.message || err));
  }
}

function onBarcodeScanned(barcode) {
  formData.value.ndcnumber = barcode;
  showScanner.value = false;
  fetchMedicationDetailsFromAPI(barcode);
}

// --------------- End of Chris additions to <script> ---------------

//const pastProvar = ref<string[]>([]);

/*watch(() => props.isEditMedication, (newVal) => {
  if (newVal) {
    editFormdata.value = { ...editFormdata.value, ...newVal }
  } else {
    resetForm()
  }
}, { immediate: true }) */

watch(() => props.show, (visible) => {
  if (!visible) resetForm()
})
watch(           
   () => props.editFormdata,
   (newValue, oldValue) => {   
        
     if (newValue && !oldValue) {         
       // Copy properties from editFormdata to formData          
     
       if(props.isEditMedication==true)
       {
        
         formData.value.medicationName = props.editFormdata.medname;
          formData.value.diagnosis = props.editFormdata.diagnosis;  
          formData.value.rxnorns = props.editFormdata.rxnorns;
          formData.value.dosage = props.editFormdata.med_amount;
          formData.value.frequency = props.editFormdata.med_frequency;
          formData.value.ndcnumber = props.editFormdata.ndcnumber;
          formData.value.quantity = props.editFormdata.total;
          formData.value.prn = props.editFormdata.prn;
          formData.value.ordernumber = props.editFormdata.order_number;
          formData.value.route = props.editFormdata.route;
          /*
          This is accounting for Intravanous Form Fields if the showIvForm and via param has a value 
          */ 
         if(props.editFormdata.via_med==1)
         {
          showIvform.value = true;
           formData.value.via = props.editFormdata.viatype;
           formData.value.fluidType = props.editFormdata.fluidtype;
           formData.value.totalVolume = props.editFormdata.totalVolumn;
           formData.value.totalVolumeUnit = props.editFormdata.totalVolumnUnit;
           formData.value.rate = props.editFormdata.rate;
           formData.value.howLong = props.editFormdata.ivhowLong;
           formData.value.startTime = props.editFormdata.ivstarttime;
           formData.value.endTime = props.editFormdata.ivendtime;

         }
          //TIme Input Prefill 
          /* Medication AdminisrtrationTimes propertis (1st choice) || yearmedTimes (jSON - backup if needed) has the Administration times 
          * Thats needed in order to prefill the time slots if Administration time already exist 
          */
          if(props.editFormdata.administrationTimes && props.editFormdata.administrationTimes !="" || props.editFormdata.administrationTimes !=null)
          {
            //extract times from the yearmedtime (no need to loop since we have a single object of the Medication instance)
            const splitted = props.editFormdata.administrationTimes.split(',');
            timeInputs.value = splitted.map(t => t.trim());
            formData.value.administrationTimes = timeInputs.value;
          }
         else {
             // timeInputs.value = []
            }
          
       }
      
       }     
      }, { deep: true }   
    );

    //------ifStatusIsChange Watch----------//
watch( [() =>props.ifStatusIsChange],
  () => {
  if(props.ifStatusIsChange==false)
  {
    alert("False");
     ifStatusIsChange.value=props.ifStatusIsChange;
  }
  else{
    alert("True");
    ifStatusIsChange.value=props.ifStatusIsChange;
  }
})
/** Reactive object storing all form fields. */


/** The four tabs: */
const tabs = [
  { value: 'medInfo',         label: 'Medication Information' },
  { value: 'prescriptionInfo',label: 'Prescription Information'},
  { value: 'providerInfo',    label: 'Provider Information'    },
  { value: 'pharmacyInfo',    label: 'Pharmacy Information'    }
]
const CURR_API = ref<string>('https://medadministration:8890/keyon');
const loadedProvider = ref<string>('');
const loadedpatientNPI = ref<string>('');
const newProvider = ref<PastProvarItem[]>([]);
  const newPharmacy = ref<PastProvarItem[]>([]);
  const pastpatPharmacy = ref<PastProvarItem[]>([]);
    const drugs = ref<PastProvarItem[]>([]);
const newProvloaded =ref<boolean>(false);
const newpastPatPharcy = ref<boolean>(false);
const  newPharmloaded = ref<boolean>(false);
const showIvform = ref<boolean>(false);
const searchTerm = ref<string>('');
const newDiagloaded = ref<boolean>(false);
const newDiagcodes = ref<PastProvarItem[]>([]);
/** Track which tab is active. */
const activeTab = ref('medInfo')

/*Handle Tab Function */
const handleTabClick = (tabValue: string) => {
  activeTab.value = tabValue; //set the active tab
  //Run or emit event when the providerInfo tab is active 
  if(tabValue === 'providerInfo') {
    loadPastProviders();
    showIvform.value = false
  }
  if(tabValue==='pharmacyInfo')
  {
    loadPatientPharmacy();
    showIvform.value = false
  }
  if(tabValue==='prescriptionInfo')
  {
    showIvform.value=false;
  }
  if(tabValue==='medInfo')
  {
    if(formData.value.route=="IV (Intravenous)")
    {
      showIvform.value=true;
    }
    else{
      showIvform.value=false;
    }
  }
};
/** Checking Route Option */
function checkRouteSelection(fdata)
{
 
  if(fdata=="IV (Intravenous)")
  {
    showIvform.value=true;
  }
  else{
    showIvform.value=false;
  }
}
function detectFreqChange(frequency:string){
 
  selectedFrequency.value = frequency;
  emit('freqchange', formData.value, frequency)
  if(props.ifStatusIsChange==true)
  {
   console.log("Status True");
    ifStatusIsChange.value =true;
  }
  else{
    ifStatusIsChange.value=false;
   console.log("Status False");
  }
}
function detectDosageChange(dosageamount:string){
  
  selectedDosage.value = dosageamount;
  emit('dosagechange', formData.value, dosageamount)
}
/** Handler for the Save button. */
function handleSave() {
  // You can do validation or other logic here
  if(props.isEditMedication)
  {
    //lets make sure the time in puts are in the formData before we send it over
    alert("Its definitely an edit");
    if(timeInputs.value.length > 0 )
    {
     
      formData.value.administrationTimes = timeInputs.value.join(',') ;
      formData.value.coreason = Reasaon4change.value;
      emit('save',formData.value,timeInputs.value,selectedFrequency.value);
    }
   // emit('save',editFormdata.value)
  }
  if(props.isAddNewMed)
  {
    emit('save',formData.value)
  }
  //resetForm()
}
/*Handles the Patient Pharmacy Select Box Change and parsing*/
function selectpastPharm()
{
  
  for(var i=0; i < pastpatPharmacy.value.length; i++)
  {
    
    if(pastpatPharmacy.value[i].npinumber==loadedpatientNPI.value)
    {
      formData.value.pharmacyName = pastpatPharmacy.value[i].pharmacyname;
      formData.value.pharmacyNpi = pastpatPharmacy.value[i].npinumber;
      formData.value.pharmacyAddress = pastpatPharmacy.value[i].pharmaddress;
     // formData.value.pharmacyOffice = pastpatPharmacy.value[i].addresses[index].tel;
     // formData.value.pharmacyCell = pastpatPharmacy.value[i].addresses[index].tel;
     newpastPatPharcy.value=false;
    }
  }
}
/*Handle Selecting the Diagnosis code and description*/
function selectDiagcode(index: number)
{
  formData.value.diagnosis = newDiagcodes.value[index].code;
  formData.value.diagdescription = newDiagcodes.value[index].description;
  //now close list container 
  newDiagloaded.value=false;
}
/*Handle SElecting a Pharmacy*/
function selectPharm(index: number)
{
      formData.value.pharmacyName = newPharmacy.value[index].name;
      formData.value.pharmacyNpi = newPharmacy.value[index].npinumber;
      formData.value.pharmacyAddress = newPharmacy.value[index].addresses[index].address;
      formData.value.pharmacyOffice = newPharmacy.value[index].addresses[index].tel;
      formData.value.pharmacyCell = newPharmacy.value[index].addresses[index].tel;
       newPharmloaded.value=false;
      
}
/*Handle NewProvider Function*/
function selectProvider(index: number)
{
      formData.value.providerName = newProvider.value[index].name;
      formData.value.providerNpi = newProvider.value[index].npinumber;
      formData.value.providerAddress = newProvider.value[index].addresses[0].address;
      formData.value.providerOffice = newProvider.value[index].addresses[0].tel;
      formData.value.providerCell = newProvider.value[index].addresses[0].tel;
      //formData.value.providerEmail = newProvider[index].email;
      newProvloaded.value=false;
     
}
function getDrugSynonym(synonym: string)
{
  console.log(`Fetching synonyms: ${synonym}`);
  formData.value.medicationName = synonym
  drugs.value =[];
}
async function fetchDiagnosis()
{
   searchTerm.value =formData.value.diagnosis;
   let content = {
    action:"CodeLookUp",
    searchTerm:searchTerm.value
   };
   if(formData.value.diagnosis=="")
   {
    newDiagcodes.value = [];  //resetting the newDiagcodes array if the input field is blank
    newDiagloaded.value=false; //Should make the code display box disappear based on the boolean
    return;
   }
   try{

    await axios
        .post( "http://20.231.24.137/med-admin/keyon/icd_calls.php", content, {
          headers: { "Content-Type": "application/json;" },
        })
        .then((res) => {
          console.log(res.data); //see what the response looks like from a data structure stanpoint
          if (res.data && res.data.result <=0) {
            newDiagcodes.value = [];  //resetting the newDiagcodes array if the input field is blank
            newDiagloaded.value=false; //Should make the code display box disappear based on the boolean
            formData.value.diagnosis="No Results Found";
          } else{ 
            
            
            newDiagcodes.value = res.data.actualResults;
            newDiagloaded.value=true;
           
           
          }
        });
   }
   catch(error){
    console.error('Error fetching Diagnosis Code:', error);
   }
}
async function fetchDrugs()
{
  if (formData.value.medicationName.trim() === '') { 
      drugs.value = [];       
       return; 
      }
      try {        
        const response = await axios.get(`https://rxnav.nlm.nih.gov/REST/drugs.json?name=${encodeURIComponent(formData.value.medicationName)}`); 
        const data = response.data;                
        if (data && data.drugGroup && data.drugGroup.conceptGroup) {  
            const conceptProperties = data.drugGroup.conceptGroup            
            .flatMap(group => group.conceptProperties || []);                   
             // Assigning to drugs array with PastProvItem type          
             drugs.value = conceptProperties.map(drug => ({            
              0: drug.rxcui,          // RXCUI           
              1: drug.name,           // Drug Name           
              2: drug.synonym         // Synonym          
              })) as PastProvarItem[];       
        } else {          
          drugs.value = [];        

        }      
      } catch (error) { 
          console.error('Error fetching drugs:', error);     
      }
}
async function showPharmacyResult()
{
  let content = {
        methname:"GetAllInfo",
        primphysician:"",
        organization: formData.value.pharmacyName, 
        postalCode:"46260",
        enumeration:"NPI-2"
      };
      // this.showPreloader()
      await axios
        .post( "http://20.231.24.137/med-admin/keyon/NPILookup.php", content, {
          headers: { "Content-Type": "application/json;" },
        })
        .then((res) => {
          if (res.data && res.data.length < 1) {
            console.log(res.data);
            newPharmacy.value = [];
          } else{ 
            
            console.log(res.data);
            newPharmacy.value = res.data;
            console.log(newPharmacy);
            newPharmloaded.value=true;
            newpastPatPharcy.value=false;
           
          }
        });
      // this.stopPreloader()
}
/*Patient assigned Pharmacy Lookup Axios Call*/
async function loadPatientPharmacy()
{
  //newPharmloaded.value=false;
   let content = {
     MedicationAdmin: {
      API_Meth:"GetAssignedPatPharmacy",
      patientid:"709081242",
      accountnumber:"904575107"
     }
   };
   await axios
        .post( "http://20.231.24.137/med-admin/keyon/tswebhook.php", content, {
          headers: { "Content-Type": "application/json;" },
        })
        .then((res) => {
 
          if (res.data.results && res.data.results =="") {
            pastpatPharmacy.value = [];
            newpastPatPharcy.value=false;
          } else{ 
            pastpatPharmacy.value = res.data.results;
           newpastPatPharcy.value=true;
           newPharmloaded.value=false;
            console.log(res.data);
           
            console.log(pastpatPharmacy);
          
           // newPharmloaded.value=false;
           
          }
        });
}
/* Npi Search Result from the NPIRegistery on click event*/
async function showNpiResult()
{
  let content = {
        methname: "GetAllInfo",
        primphysician: formData.value.providerName, 
      };
      // this.showPreloader()
      await axios
        .post( "http://20.231.24.137/med-admin/keyon/NPILookup.php", content, {
          headers: { "Content-Type": "application/json;" },
        })
        .then((res) => {
          if (res.data.results && res.data.results == "No Results Found") {
            newProvider.value = [];
          } else{ 
            emit('updtPastProvbool');
            console.log(res.data);
            newProvider.value = res.data;
            console.log(newProvider);
            newProvloaded.value=true;
           
          }
        });
      // this.stopPreloader()
}
/*function to handle the on change event on the provider select box - Fill PRovider information into the Provider form */
function fillProviderInfo()
{
  for(var i=0; i < props.pastProvar.length;i++)
  {
    let name = props.pastProvar[i]["firstname"] + ' ' + props.pastProvar[i]["lastname"];
    if(loadedProvider.value == name)
    {
      
      console.log(props.pastProvar[i]);
      console.log(props.pastProvar[i].npinumber);
      formData.value.providerName = name;
      formData.value.providerNpi = props.pastProvar[i].npinumber;
      formData.value.providerAddress = props.pastProvar[i].addr1;
      formData.value.providerOffice = props.pastProvar[i].tel;
      formData.value.providerCell = props.pastProvar[i].tel;
      formData.value.providerEmail = props.pastProvar[i].email;
      emit('updtPastProvbool');
    }
  }
}
/*Function to go and lookup past providers */
function loadPastProviders()
{
  
  emit('loadprov');
}
</script>

<style scoped>
/* Basic fade for the modal appear/disappear */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
.selectdisplay2-true{
  display:flex;
  width:220px;
}
/*select field dynamci css*/
.selectdisplay-true{
  width: 100%;
    height: 220px;
    overflow-y:scroll;
    color: black;
    display: flex;
    flex-direction: column;
    border-style:solid;
    border-width:1px;
    border-color:#6c757d
}
.selectdisplay-false{
  display:none;
}
.pastProvdisplay-true{
  display:block;
}
.pastProvdisplay-false{
  display:none;
}
/* Modal Overlay & Container */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.modal-content {
  background-color: #fff;
  width: 80%;
  max-width: 800px;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
  max-height: 80%;
  overflow: scroll;
}
.modal-title {
  margin-top: 0;
  text-align: center;
  margin-bottom: 1rem;
}

/* Tabs (buttons) */
.tabs {
  display: flex;
  margin-bottom: 1rem;
  border-bottom: 1px solid #ccc;
}
.tab-button {
  flex: 1;
  text-align: center;
  background: none;
  border: none;
  padding: 0.75rem;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}
.tab-button:hover {
  background-color: #f2f2f2;
}
.tab-button.active {
  background-color: #e6f4f4;
  border-bottom: 3px solid #0c8687;
}

/* Tab panel content */
.tab-panel {
  margin: 1rem 0;
}
.section-title {
  margin: 1rem 0;
  font-size: 1.1rem;
  border-bottom: 2px solid #0c8687;
  color: #333;
  padding-bottom: 0.5rem;
}

/* Form layout */
.form-group {
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
}
.form-group label {
  font-weight: 500;
  margin-bottom: 0.3rem;
}
.required { color: red; }
.form-group input,
.form-group select {
  padding: 0.4rem 0.6rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
/* Horizontal row of fields */
.form-row {
  display: flex;
  gap: 1rem;
}
.form-row .form-group {
  flex: 1;
}
.time-input-row {
  margin-bottom: 0.5rem;
}
.time-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}
.change-reason{
  width:75%;
}
/* Bottom action buttons */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}
.btn-cancel,
.btn-save {
  padding: 0.6rem 1.2rem;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  font-weight: 500;
}
.btn-cancel {
  background-color: #6c757d;
  color: #fff;
}
.btn-cancel:hover {
  background-color: #5a6268;
}
.btn-save {
  background-color: #0c8687;
  color: #fff;
}
.btn-save:hover {
  background-color: #0a7273;
}

/* Chris additions */
.dosage-group { display: flex; flex-direction: column; }
.dosage-row {
  display: flex; gap: 0.5rem; align-items: center;
}
.dosage-input { flex: 0 0 80px; }
.dosage-select { flex: 1; }

.volume-rate-row { display: flex; gap: 1rem; }
.volume-group { display: flex; flex-direction: column; }
.volume-row {
  display: flex; gap: 0.5rem; align-items: center;
}
.volume-dropdown { width: 70px; }

.btn-save:disabled {
  background-color: #b2d8d8;
  cursor: not-allowed;
}

.camera-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.2rem;
  display: flex;
  align-items: center;
  transition: background 0.2s;
}
.camera-btn:hover {
  background: #e6f4f4;
  border-radius: 4px;
}

.verify-btn {
  background: #0c8687;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 0.3rem 0.9rem;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}
.verify-btn:disabled {
  background: #b2d8d8;
  cursor: not-allowed;
}
.verify-btn:hover:not(:disabled) {
  background: #0a7273;
}

.autocomplete {
  position: relative;
}
.autocomplete-input {
  width: 100%;
}
.suggestions-list {
  position: absolute;
  top: 100%;
  left: 0; right: 0;
  background: #fff;
  border: 1px solid #ccc;
  border-top: none;
  max-height: 200px;
  overflow-y: auto;
  z-index: 1000;
  margin: 0; padding: 0;
  list-style: none;
}
.suggestion-item {
  padding: 8px;
  cursor: pointer;
  border-bottom: 1px solid #eee;
}
.suggestion-item:last-child {
  border-bottom: none;
}
.suggestion-item:hover {
  background: #f5f5f5;
}
.suggestion-item strong {
  display: block;
}
.suggestion-item small {
  color: #666;
  font-size: 0.85em;
  display: block;
}
.suggestion-header {
  display: flex;
  align-items: baseline;
  gap: 6px;
}
.suggestion-ingredients {
  font-size: 0.85em;
  color: #555;
}
.suggestion-ndc {
  font-size: 0.85em;
  color: #777;
  margin-top: 2px;
}

/* Mobile‐only section styling */
.mobile-section {
  border: 1px solid #ccc;
  border-radius: 4px;
  margin-bottom: 1rem;
  overflow: hidden;
}
.mobile-section-header {
  background: #e6f4f4;
  padding: 0.75rem;
  font-weight: 600;
  cursor: pointer;
}
.mobile-section-body {
  padding: 1rem;
  background: #fff;
}

/* Basic form groups */
.mobile-form-group {
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
}
.mobile-form-group label {
  margin-bottom: 0.25rem;
  font-size: 0.95rem;
}
.mobile-input,
.mobile-select {
  padding: 0.6rem;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}

/* Inline fields (e.g. dosage+unit, NDC+buttons) */
.mobile-inline-group {
  display: flex;
  gap: 0.5rem;
}
.dosage-input,
.volume-input {
  flex: 0 0 4rem;
}
.mobile-select {
  flex: 1;
}

/* Scanner & verify buttons */
.mobile-icon-btn {
  background: none;
  border: none;
  padding: 0.2rem;
  cursor: pointer;
}
.mobile-verify-btn {
  background: #0c8687;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 0.5rem 0.75rem;
}
.mobile-verify-btn:disabled {
  background: #b2d8d8;
  cursor: not-allowed;
}

/* Autocomplete suggestions override */
.mobile-autocomplete {
  position: relative;
}
.mobile-suggestions-list {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #ccc;
  max-height: 180px;
  overflow-y: auto;
  z-index: 10;
}
.mobile-suggestion-item {
  padding: 0.5rem;
  cursor: pointer;
}
.mobile-suggestion-item:hover {
  background: #f5f5f5;
}
.mobile-suggestion-header {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}
.mobile-suggestion-ingredients {
  font-size: 0.85rem;
  color: #555;
}
.mobile-suggestion-ndc {
  font-size: 0.85rem;
  color: #777;
  margin-top: 0.25rem;
}

/* IV sub‐section */
.mobile-subsection {
  border-top: 1px solid #ddd;
  padding-top: 1rem;
  margin-top: 1rem;
}
.mobile-subsection-title {
  font-size: 1rem;
  margin-bottom: 0.75rem;
}

/* ----- MOBILE‐ONLY MODAL ADJUSTMENTS ----- */
@media (max-width: 767px) {
  /* shrink & scroll the modal itself */
  .modal-content {
    width: 95vw;
    max-width: 380px;
    padding: 1rem;
    max-height: 90vh;
    overflow-y: auto;
    box-sizing: border-box;
  }

  /* make every input & select stretch */
  .mobile-input,
  .mobile-select {
    width: 100%;
    box-sizing: border-box;
  }

  /* inline groups now wrap */
  .mobile-inline-group {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }
  /* default to full‐width for each child */
  .mobile-inline-group > * {
    flex: 1 1 100%;
  }
  /* optional: refine specific fields */
  .mobile-inline-group .dosage-input {
    flex: 0 1 40%;
  }
  .mobile-inline-group .mobile-select {
    flex: 0 1 58%;
  }
}
</style>