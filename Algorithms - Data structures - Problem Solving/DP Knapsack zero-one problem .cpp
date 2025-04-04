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
int w[1003], v[1003];
int n;
int NS;

int main() {

	cin >> n >> NS;

	for (int i = 1; i <= n;i++)
		cin >> w[i];

	for (int i = 1; i <= n;i++)
		cin >> v[i];

	for (int i = 0; i <= n; i++) 
		for (int j = 0; j <= NS; j++) 
			if (i==0 || j==0 )
				dp[i][0] = 0;
			else 
				dp[i][j]=(w[i]>j ? dp[i - 1][j]: max(dp[i - 1][j], dp[i - 1][j - w[i]] + v[i]));
			
	

	vector<int>Items;

	int j = NS;
	for (int i = n; i >= 0; i--) {

		if (dp[i][j] > dp[i - 1][j]) {
			Items.push_back(i);
			j -= w[i];
		}

	}


	cout << endl;
	cout << dp[n][NS] << endl;

	reverse(Items.begin(), Items.end());

	for (int i = 0; i < Items.size();i++)
		cout << Items[i] << " ";

	
	return 0;
}