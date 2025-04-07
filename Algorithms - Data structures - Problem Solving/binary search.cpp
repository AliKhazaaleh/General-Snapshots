/*
    Date: 2015
*/

// Recursive Binary Search
bool binarySearch(int arr[], int value, int start, int end) {
    if (start > end) return false;

    int mid = start + (end - start) / 2;

    if (arr[mid] > value)
        return binarySearch(arr, value, start, mid - 1);
    else if (arr[mid] < value)
        return binarySearch(arr, value, mid + 1, end);
    else
        return true;
}

// Function needs to be defined: int f(int x);
int binarySearchFirst(int start, int end, int val) {
    while (start < end) {
        int mid = start + (end - start) / 2;
        if (f(mid) < val)
            start = mid + 1;
        else
            end = mid;
    }
    return start;
}


int binarySearchLast(int start, int end, int val) {
    while (start < end) {
        int mid = start + (end - start + 1) / 2;
        if (f(mid) > val)
            end = mid - 1;
        else
            start = mid;
    }
    return start;
}


// Function needs to be defined: bool can(ll x);
ll solution = -1;

void binarySearch(ll start, ll end) {
    if (start > end) return;

    ll mid = (start + end) / 2;

    if (can(mid)) {
        solution = mid;
        binarySearch(mid + 1, end); // Maximize value
    } else {
        binarySearch(start, mid - 1);
    }
}


// Function needs to be defined: double f(double x);
const double EPS = 1e-9;

double binarySearchDouble(double start, double end, double val) {
    while (fabs(end - start) > EPS) {
        double mid = start + (end - start) / 2;
        if (f(mid) < val)
            start = mid;
        else
            end = mid;
    }
    return start;
}


// Function needs to be defined: bool can(double x);
double binarySearchCondition(double start, double end) {
    for (int i = 0; i < 100; i++) {
        double mid = (start + end) / 2;
        if (can(mid))
            end = mid;
        else
            start = mid;
    }

    return can(end) ? start : -1.0;
}

