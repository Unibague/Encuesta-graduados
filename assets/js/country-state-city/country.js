import countryList from './country-data.js';
import { compare, findEntryByCode } from './utils.js';
// Get a country by isoCode.
function getCountryByCode(isoCode) {
    if (!isoCode)
        return undefined;
    return findEntryByCode(countryList, isoCode);
}
// Get a list of all countries.
function getAllCountries() {
    return countryList;
}
function sortByIsoCode(countries) {
    return countries.sort((a, b) => {
        return compare(a, b, (entity) => {
            return entity.isoCode;
        });
    });
}
export default {
    getCountryByCode,
    getAllCountries,
    sortByIsoCode,
};
