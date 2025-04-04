#include <iostream>
#include <vector>

using namespace std;

using ll = long long;

ll n, m;
ll p, q;
ll e, d;
ll phi_n;

vector<ll> primes;

bool isPrime(ll num) {
    if (num == 2) return true;
    if (num < 2 || num % 2 == 0) return false;

    for (ll i = 3; i * i <= num; i += 2) {
        if (num % i == 0) return false;
    }

    return true;
}

bool findPQ() {
    ll currentP = primes.back();
    ll index = primes.size() - 1;

    while (index >= 0) {
        if (primes[index] * currentP == n) {
            ::p = currentP;
            ::q = primes[index];
            return true;
        }
        index--;
    }

    return false;
}

bool findED() {
    ll currentE = primes.back();
    ll index = primes.size() - 1;

    while (index >= 0) {
        if ((currentE * primes[index]) % phi_n == 1) {
            ::e = currentE;
            ::d = primes[index];
            return true;
        }
        index--;
    }

    return false;
}

int main() {
    cout << "-------------------- RSA --------------------" << endl;
    cout << "Enter the number n (n = p * q, where p and q are primes):" << endl;
    cout << ">>> ";
    cin >> n;

    cout << "Enter the number m (plaintext number):" << endl;
    cout << ">>> ";
    cin >> m;

    bool foundPQ = false;
    for (ll i = 3; !foundPQ; i += 2) {
        if (isPrime(i)) {
            primes.push_back(i);
            if (findPQ()) {
                foundPQ = true;
            }
        }
    }

    cout << "\n---------------- Solution ----------------" << endl;
    cout << "p = " << p << endl;
    cout << "q = " << q << endl;
    cout << "n = p * q = " << p << " * " << q << " = " << (p * q) << endl;

    phi_n = (p - 1) * (q - 1);
    cout << "phi(n) = (p - 1) * (q - 1) = " << (p - 1) << " * " << (q - 1)
         << " = " << phi_n << endl;

    primes.clear();
    bool foundED = false;
    for (ll i = 3; !foundED; i += 2) {
        if (isPrime(i)) {
            primes.push_back(i);
            if (findED()) {
                foundED = true;
            }
        }
    }

    cout << "\ne = " << e << endl;
    cout << "d = " << d << endl;
    cout << "Verification: (e * d) mod phi(n) = ("
         << e << " * " << d << ") mod " << phi_n
         << " = " << (e * d) % phi_n << endl;

    cout << "\nEncryption and Decryption Equations:" << endl;
    cout << "cipher = (m^e) mod n = (" << m << "^" << e << ") mod " << n << endl;
    cout << "plain  = (cipher^d) mod n = m = (" << "cipher^" << d << ") mod "
         << n << " = " << m << endl;

    cout << "\nNote: This program does not implement actual encryption/decryption due to overflow issues in C++." << endl;

    return 0;
}
