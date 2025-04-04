#include <iostream>
#include <string>
#include <algorithm>
#include <set>
#include <map>
#include <vector>
#include <deque>
#include <queue>
#include <cctype>
#include <stdlib.h>
#include <stdio.h>
#include <stack>
#include <cmath>
#include <ctime>

using namespace std;
typedef  long long ll;

int dp[1003][1003];

int main() {

	string x, y;
	cin >> x >> y;

	int lensub = 0,endsub;

	int n = x.size();
	int m = y.size();

	for (int i = 0; i <= n;i++)
		for (int j = 0; j <= m;j++) {
		
			if (i == 0 || j == 0)
				dp[i][j] = 0;
			else
				if (x[i - 1] == y[j - 1]) {
				
					dp[i][j] = dp[i - 1][j - 1] + 1;

					if (dp[i][j] > lensub) {
						lensub = dp[i][j];
						endsub = i;
					}
				
				}
					else
					dp[i][j] = 0;
		}
	


	string ans = "";

	for (int i = endsub - lensub ;i < endsub; i++)
		ans += x[i];
	
	
	cout << ans << "  " << lensub << endl;
	/*


	5 10
	3 5 6 3 2
	2 4 4 3 2
	*/
	/*
	for (int i = 0; i <= n;i++) {
		for (int j = 0; j <= NS;j++)
			cout << dp[i][j] << " ";

		cout << endl;
	}


	//int start_s = clock();
	//int stop_s = clock();
	// cout << "time: " << (stop_s - start_s) / double(CLOCKS_PER_SEC) * 1000 << endl;
	*/
	return 0;
}