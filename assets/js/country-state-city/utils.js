export const findEntryByCode = (source, code) => {
    if (code && source != null) {
        const codex = source.findIndex((c) => {
            return c.isoCode === code;
        });
        return codex !== -1 ? source[codex] : undefined;
    }
    return undefined;
};
export const findStateByCodeAndCountryCode = (source, code, countryCode) => {
    if (code && countryCode && source != null) {
        const codex = source.findIndex((c) => {
            return c.isoCode === code && c.countryCode === countryCode;
        });
        return codex !== -1 ? source[codex] : undefined;
    }
    return undefined;
};
export function defaultKeyToCompare(entity) {
    return entity.name;
}
export const compare = (a, b, 
// eslint-disable-next-line no-unused-vars
keyToCompare = defaultKeyToCompare) => {
    if (keyToCompare(a) < keyToCompare(b))
        return -1;
    if (keyToCompare(a) > keyToCompare(b))
        return 1;
    return 0;
};
// Iterative (non-recursive) merge sort. Safari/iOS JavaScriptCore's native
// Array.prototype.sort with a custom comparator can throw
// "RangeError: Maximum call stack size exceeded" on large, already-ordered
// arrays (e.g. sorting all ~19,800 US cities) because its internal sort
// falls back to a recursive algorithm whose call depth grows with the
// array size. This implementation sorts bottom-up with plain loops, so its
// call stack stays flat no matter how large the array is.
export const mergeSort = (arr, comparator = compare) => {
    const n = arr.length;
    if (n < 2) return arr.slice();

    let source = arr.slice();
    let target = new Array(n);

    for (let width = 1; width < n; width *= 2) {
        for (let i = 0; i < n; i += 2 * width) {
            const left = i;
            const mid = Math.min(i + width, n);
            const right = Math.min(i + 2 * width, n);

            let a = left;
            let b = mid;
            let k = left;

            while (a < mid && b < right) {
                target[k++] = comparator(source[a], source[b]) <= 0 ? source[a++] : source[b++];
            }
            while (a < mid) target[k++] = source[a++];
            while (b < right) target[k++] = source[b++];
        }
        const tmp = source;
        source = target;
        target = tmp;
    }

    return source;
};
export const convertArrayToObject = (keys, arr) => {
    const result = arr.map((subArr) => {
        return Object.fromEntries(keys.map((key, index) => [key, subArr[index]]));
    });
    return result;
};
