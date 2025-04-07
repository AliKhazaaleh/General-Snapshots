/*
	Date: 2015
*/
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

int dp[10004][10004];

int main() {
	string X,Y;
	string ans = "";
	int i, j;
	

	cin >> X >>Y;
	
	int m = X.size();
	int n = Y.size();

	for (i = 0; i <= m; i++)
		for (j = 0; j <= n; j++)
			if (i == 0 || j == 0)
				dp[i][j] = 0;
			else
			    dp[i][j]=(X[i - 1] == Y[j - 1]?dp[i - 1][j - 1] + 1:max(dp[i - 1][j], dp[i][j - 1]));
	            //                                  diagonal  +   1 else max ( up   , left        )
		

	
	
	cout << endl;
	cout << dp[m][n]<<endl;

	
	i = m, j = n;


	while (i >=1 && j >=1) {
	
		if (X[i - 1] == Y[j - 1]) {
			ans += X[i - 1];
			i--, j--;
		}
		else 
			 (dp[i - 1][j] > dp[i][j - 1]) ? i--:j--;

	}

	reverse(ans.begin(), ans.end());

	cout <<ans ;


return 0;
}
