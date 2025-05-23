<template>
  <transition name="fade">
    <div v-if="show" class="modal-overlay">
      <div class="modal-content">
        <!-- Heading -->
        <h2 class="modal-title">
          {{ isEditMode ? 'Edit Medication' : 'Add New Medication' }}
        </h2>

        <!-- Tabs -->
        <div class="tabs">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            class="tab-button"
            :class="{ active: activeTab === tab.value }"
            @click="activeTab = tab.value"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- TAB 1: Medication Information -->
        <div v-if="activeTab === 'medInfo'" class="tab-panel">
          <h3 class="section-title">Medication Information</h3>

          <!-- Medication Name (required) -->
          <div class="form-group">
            <label>
              Medication Name <span class="required">*</span>
            </label>
            <input
              type="text"
              v-model="formData.medicationName"
              placeholder="Medication Name"
              required
              aria-required="true"
            />
          </div>

          <!-- Dosage + Frequency row -->
          <div class="form-row">
            <div class="form-group dosage-group">
              <label>
                Dosage <span class="required">*</span>
              </label>
              <div class="dosage-row">
                <input
                  type="text"
                  v-model="formData.dosage"
                  placeholder="Dosage"
                  class="dosage-input"
                  required
                  aria-required="true"
                />
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
            </div>
            <div class="form-group">
              <label>
                Frequency <span class="required">*</span>
              </label>
              <select
                v-model="formData.frequency"
                required
                aria-required="true"
              >
                <option value="">Select frequency</option>
  <option>1 time daily</option>
  <option>2 times daily</option>
  <option>2 times daily, as needed (PRN)</option>
  <option>3 times a day</option>
  <option>3 times a day, as needed for headache (PRN)</option>
  <option>3 times daily</option>
  <option>3 times daily, as needed (PRN)</option>
  <option>4 times a day</option>
  <option>4 times daily</option>
  <option>4 times daily, as needed (PRN)</option>
  <option>as directed</option>
  <option>as needed</option>
  <option>as one dose on the first day then take one tablet daily thereafter</option>
  <option>at bedtime</option>
  <option>at bedtime, as needed (PRN)</option>
  <option>at bedtime as needed for sleep (PRN)</option>
  <option>before every meal</option>
  <option>bi-weekly</option>
  <option>constant infusion</option>
  <option>daily</option>
  <option>daily, as needed (PRN)</option>
  <option>daily as directed</option>
  <option>every day</option>
  <option>every month</option>
  <option>every other day</option>
  <option>every morning</option>
  <option>every evening</option>
  <option>every hour</option>
  <option>every hour, as needed (PRN)</option>
  <option>every 2 hours</option>
  <option>every 2 hours, as needed (PRN)</option>
  <option>every 3 hours</option>
  <option>every 3 hours, as needed (PRN)</option>
  <option>every 4 hours</option>
  <option>every 4 hours, as needed (PRN)</option>
  <option>every 4 to 6 hours, as needed for pain (PRN)</option>
  <option>every 4 to 6 minutes</option>
  <option>every 4 to 8 hours</option>
  <option>every 6 hours</option>
  <option>every 6 hours, as needed for pain (PRN)</option>
  <option>every 6 hours, as needed for cough (PRN)</option>
  <option>every 8 hours</option>
  <option>every 8 hours, as needed (PRN)</option>
  <option>every 12 hours</option>
  <option>every 12 hours, as needed (PRN)</option>
  <option>every 24 hours</option>
  <option>every 24 hours, as needed (PRN)</option>
  <option>every Monday, Wednesday, Friday, Sunday</option>
  <option>every Tuesday, Thursday, Saturday</option>
  <option>before breakfast, lunch, dinner</option>
  <option>after breakfast, lunch, dinner</option>
  <option>Friday</option>
  <option>Monday</option>
  <option>once a week</option>
  <option>one time dose</option>
  <option>Saturday</option>
  <option>Sunday</option>
  <option>three times a week</option>
  <option>Thursday</option>
  <option>Tuesday</option>
  <option>twice daily</option>
  <option>twice daily, as needed for nausea (PRN)</option>
  <option>two times a week</option>
  <option>use as directed per instructions in pack</option>
  <option>Wednesday</option>
  <option>weekly</option>
              </select>
            </div>
          </div>

          <!-- Route & Duration row -->
          <div class="form-row">
            <div class="form-group">
              <label>
                Route <span class="required">*</span>
              </label>
              <select
                v-model="formData.route"
                required
                aria-required="true"
              >
                <option value="">Select route</option>
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

          <!-- NDC, RX Norm, Diagnosis in one row -->
          <div class="form-row">
            <div class="form-group">
              <label>NDC Number</label>
              <input
                type="text"
                v-model="formData.ndcNumber"
                placeholder="NDC Number"
              />
            </div>
            <div class="form-group">
              <label>RX Norm</label>
              <input
                type="text"
                v-model="formData.rxNorm"
                placeholder="RX Norm"
              />
            </div>
            <div class="form-group">
              <label>Diagnosis</label>
              <input
                type="text"
                v-model="formData.diagnosis"
                placeholder="Diagnosis"
              />
            </div>
          </div>

          <!-- PRN if route is in [IVI, SQ, IM, ID, TOP] -->
          <div
            v-if="!['IV (Intravenous)','Oral/Sublingual'].includes(formData.route)"
            class="form-group checkbox-group"
          >
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
                placeholder="0"
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
          <div v-if="formData.route === 'IV (Intravenous)'">
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

            <!-- Volume + Rate row -->
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

        <!-- TAB 2: Prescription Info -->
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

        <!-- TAB 3: Provider Info -->
        <div v-if="activeTab === 'providerInfo'" class="tab-panel">
          <h3 class="section-title">Provider Information</h3>
          <div class="form-group">
            <label>Provider Name</label>
            <input
              type="text"
              v-model="formData.providerName"
              placeholder="Provider Name"
            />
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
              v-model"text"
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

        <!-- TAB 4: Pharmacy Info -->
        <div v-if="activeTab === 'pharmacyInfo'" class="tab-panel">
          <h3 class="section-title">Pharmacy Information</h3>
          <div class="form-group">
            <label>Pharmacy Name</label>
            <input
              type="text"
              v-model="formData.pharmacyName"
              placeholder="Pharmacy Name"
            />
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

        <!-- Save/Cancel Buttons -->
        <div class="form-actions">
          <button class="btn-cancel" @click="handleCancel">
            Cancel
          </button>
          <button
            class="btn-save"
            @click="handleSave"
            :disabled="!isFormValid"
          >
            Save
          </button>
        </div>

      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue'
import { PastProvarItem } from '../types';
import axios from 'axios';

/** Define the structure of all form fields. */
interface MedicationFormData {
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
}
//const pastProvar = ref<string[]>([]);
/**
 * Props:
 *  show: controls visibility of the modal
 */
const props = defineProps<{
  show: boolean;
  pastProvloaded:boolean;
  pastProvar: PastProvarItem[];
}>()

/**
 * Emits:
 *  close  -> for closing/canceling the modal
 *  save   -> sends the entire formData object
 */
const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'save', payload: MedicationFormData): void;
  (e: 'loadprov'): void;
  (e: 'updtPastProvbool'):void;
}>()

/** Reactive object storing all form fields. */
const formData = ref<MedicationFormData>({
  medicationName: '',
  ndcnumber: '',
  rxnorns: '',
  diagnosis: '',
  diagdescription:'',
  dosage: '',
  frequency: '',
  route: 'Oral/Sublingual',
  prn: false,
  quantity: 0,

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
  }
  if(tabValue==='pharmacyInfo')
  {
    loadPatientPharmacy();
  }
};
/** Handler for the Save button. */
function handleSave() {
  // You can do validation or other logic here
  emit('save', formData.value)
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
        .post( "https://medadministration:8890/keyon/icd_calls.php", content, {
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
        .post( "https://medadministration:8890/keyon/NPILookup.php", content, {
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
        .post( "https://medadministration:8890/keyon/tswebhook.php", content, {
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
        .post( "https://medadministration:8890/keyon/NPILookup.php", content, {
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
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

.modal-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex; justify-content: center; align-items: center;
  z-index: 1000;
}
.modal-content {
  background-color: #fff;
  width: 80%; max-width: 800px;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
  max-height: 80%;
  overflow: scroll;
}
.modal-title {
  margin-top: 0; text-align: center; margin-bottom: 1rem;
}

.tabs {
  display: flex; margin-bottom: 1rem; border-bottom: 1px solid #ccc;
}
.tab-button {
  flex: 1; text-align: center; background: none; border: none;
  padding: 0.75rem; cursor: pointer; font-weight: 500; transition: background-color 0.2s;
}
.tab-button:hover { background-color: #f2f2f2; }
.tab-button.active {
  background-color: #e6f4f4;
  border-bottom: 3px solid #0c8687;
}

.tab-panel { margin: 1rem 0; }
.section-title {
  margin: 1rem 0; font-size: 1.1rem;
  border-bottom: 2px solid #0c8687;
  color: #333; padding-bottom: 0.5rem;
}

.form-group {
  margin-bottom: 1rem; display: flex; flex-direction: column;
}
.form-group label { font-weight: 500; margin-bottom: 0.3rem; }
.required { color: red; }
.form-group input, .form-group select {
  padding: 0.4rem 0.6rem; border: 1px solid #ccc; border-radius: 4px;
}
.checkbox-group {
  display: flex; align-items: center; gap: 0.5rem;
}
.form-row { display: flex; gap: 1rem; }
.form-row .form-group { flex: 1; }

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

.form-actions {
  display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;
}
.btn-cancel, .btn-save {
  padding: 0.6rem 1.2rem; border-radius: 4px; border: none; cursor: pointer; font-weight: 500;
}
.btn-cancel {
  background-color: #6c757d; color: #fff;
}
.btn-cancel:hover {
  background-color: #5a6268;
}
.btn-save {
  background-color: #0c8687; color: #fff;
}
.btn-save:hover {
  background-color: #0a7273;
}
.btn-save:disabled {
  background-color: #b2d8d8;
  cursor: not-allowed;
}
</style>